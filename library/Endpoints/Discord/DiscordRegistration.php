<?php namespace NozCore\Endpoints\Discord;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use NozCore\Endpoint;
use NozCore\Frontend\UrlManager;
use NozCore\Message\Error;
use NozCore\Objects\Users\User;
use Wohali\OAuth2\Client\Provider\Discord;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class DiscordRegistration extends Endpoint {

    public function get() {
        $provider = new Discord([
            'clientId'     => $GLOBALS['config']->discord['application']['clientId'],
            'clientSecret' => $GLOBALS['config']->discord['application']['clientSecret'],
            'redirectUri'  => $GLOBALS['config']->discord['application']['redirectUri']
        ]);

        if(!isset($_REQUEST['code'])) {
            $authUrl = $provider->getAuthorizationUrl([
                'scope' => ['email', 'identify'] // , 'guilds.join'
            ]);
            $_SESSION['oauth2state'] = $provider->getState();
            header("Location: {$authUrl}");
            exit;
        } else if(empty($_REQUEST['state']) || $_REQUEST['state'] !== $_SESSION['oauth2state']) {
            unset($_SESSION['oauth2state']);
            new Error('Invalid Oauth2 state.');
        } else {
            try {
                /** @var AccessToken $token */
                $token = $provider->getAccessToken('authorization_code', [
                    'code' => $_REQUEST['code']
                ]);

                /** @var DiscordResourceOwner $discordUser */
                $discordUser = $provider->getResourceOwner($token);

                $username = $discordUser->getUsername();
                if ($discordUser->getVerified()) {
                    $email = $discordUser->getEmail();
                }

                if (isset($_SESSION['user']['userId'])) {
                    // User is already authenticated, so we just update the existing user with the Discord ID.
                    $user = new User();
                    $user = $user->get($_SESSION['user']['userId']);
                    $user->setProperty('discordId', $discordUser->getId());
                    $user->save('SERVER');
                } else {
                    // User is not authenticated, so we pass the username, email and refresh token from the Discord API.
                    $urlManager = new UrlManager();

                    if ($urlManager->getRegistrationUrl()) {
                        $url = $urlManager->getRegistrationUrl() . "/refresh-token/{$token->getRefreshToken()}/username/{$username}";
                        if (isset($email)) {
                            $url .= "/email/{$email}";
                        }

                        header("Location: {$url}");
                        exit;
                    } else {
                        new Error('Registration URL is not configured. If you\'re the owner of this API, you should configure the registration URL for the frontend.');
                        exit;
                    }
                }
            } catch(IdentityProviderException $e) {
                new Error('Unable to identify your Discord account. Probably wrong authentication code.');
            } catch(\Exception $e) {
                new Error('Something went wrong while authenticating your Discord account.');
            }
        }
    }
}