<?php namespace NozCore\Endpoints\Discord;

use League\OAuth2\Client\Token\AccessToken;
use NozCore\Endpoint;
use NozCore\Message\Error;
use Wohali\OAuth2\Client\Provider\Discord;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class DiscordRegistration extends Endpoint {

    /**
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function get() {
        $provider = new Discord([
            'clientId'     => $GLOBALS['config']->discord['application']['clientId'],
            'clientSecret' => $GLOBALS['config']->discord['application']['clientSecret'],
            'redirectUri'  => $GLOBALS['config']->discord['application']['redirectUri']
        ]);

        if(!isset($_REQUEST['code'])) {
            $authUrl = $provider->getAuthorizationUrl([
                'scope' => ['email', 'identify', 'guilds.join']
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

                /** @var DiscordResourceOwner $user */
                $user = $provider->getResourceOwner($token);

                $url = "https://site.beta.eldrios.com/sign-up/id/{$user->getId()}/username/{$user->getUsername()}";

                if($user->getVerified()) {
                    $url .= "/email/{$user->getEmail()}";
                }
                header("Location: {$url}");
                exit;
            } catch(\Exception $ex) {
                new Error('Failed to get Discord user.');
            }
        }
    }
}