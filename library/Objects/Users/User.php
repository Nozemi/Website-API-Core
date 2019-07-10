<?php namespace NozCore\Objects\Users;

use ClanCats\Hydrahon\Builder;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use NozCore\DataTypes;
use NozCore\MailFactory;
use NozCore\Message\Error;
use NozCore\ObjectBase;
use Wohali\OAuth2\Client\Provider\Discord;

/**
 * Class User
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property int $authyId
 * @property boolean $verified
 * @property string $emailToken
 * @property string discordId
 *
 * @package NozCore\Objects\Users
 */
class User extends ObjectBase {

    protected $table = 'api_user';

    protected $hooks = [
        'BEFORE_SAVE_EVENT' => [
            'hashPassword',
            'validateUsername',
            'validateEmail',
        ],
        'BEFORE_SAVE_WITHOUT_ID_EVENT' => [
            'generateEmailToken',
            'checkDiscordAuthToken'
        ],
        'AFTER_SAVE_NEW_EVENT' => [
            'sendVerificationEmail'
        ]
    ];

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {
        return [
            'id'           => DataTypes::INTEGER,
            'username'     => DataTypes::STRING,
            'password'     => DataTypes::STRING,
            'email'        => DataTypes::STRING,
            'groupId'      => DataTypes::INTEGER,
            'twoFAEnabled' => DataTypes::BOOLEAN,
            'authyId'      => DataTypes::INTEGER,
            'registered'   => DataTypes::TIMESTAMP,
            'lastVisit'    => DataTypes::TIMESTAMP,
            'discordId'    => DataTypes::STRING,
            'verified'     => DataTypes::BOOLEAN,
            'emailToken'   => DataTypes::STRING
        ];
    }

    public function hashPassword() {
        if(!isset($this->id)) {
            if(isset($this->password) && strlen($this->password) > 0) {
                $this->password = password_hash($this->password, PASSWORD_BCRYPT);
            } else {
                new Error('You need to provide a password in order to register.');
            }
        }
    }

    /**
     * @return bool
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function validateUsername() {
        if(isset($this->username)) {
            if(isset($this->id)) {
                /** @var User $user */
                $user = $this->get($this->id);
                if($user->username == $this->username) {
                    return true;
                }
            }

            $query = $this->dbTable->select()->where('username', $this->username)->execute();
            if(!empty($query)) {
                new Error('Username is already taken.');
            }
        } else {
            new Error('Please provide a username in order to register.');
        }
    }

    /**
     * @return bool
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function validateEmail() {
        if(isset($this->email)) {
            if(isset($this->id)) {
                /** @var User $user */
                $user = $this->get($this->id);
                if($user->email == $this->email) {
                    return true;
                }
            }
            if(filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $query = $this->dbTable->select()->where('email', $this->email)->execute();
                if(!empty($query)) {
                    new Error('Email is already taken.');
                }
            } else {
                new Error('Please provide a valid email address.');
            }
        } else {
            new Error('Please provide an email address in order to register.');
        }
    }

    /**
     * @param bool $username
     * @return User|null
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function getUserByName($username = false) {
        if(isset($this->username) && !$username) {
            $username = $this->username;
        }

        if(!$username) {
            new Error('You need to specify a username.');
        }

        if($this->db instanceof Builder) {
            $query = $this->dbTable->select()->where('username', $username)->execute();
            return new User(end($query));
        }
        return null;
    }

    /**
     * @param $username
     * @param $password
     * @return bool|User|null
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function login($username, $password) {
        if(isset($this->username)) {
            $user = $this->getUserByName($username);

            if($user instanceof User && isset($user->password) && password_verify($password, $user->password)) {
                if(!$user->verified) {
                    new Error('Your account is not verified. Please verify your account first.');
                    return false;
                }

                return true;
            }
        }

        return false;
    }

    public function checkDiscordAuthToken() {
        if(isset($GLOBALS['data']['refreshToken'])) {
            $provider = new Discord([
                'clientId'     => $GLOBALS['config']->discord['application']['clientId'],
                'clientSecret' => $GLOBALS['config']->discord['application']['clientSecret'],
                'redirectUri'  => $GLOBALS['config']->discord['application']['redirectUri']
            ]);

            try {
                /** @var AccessToken $token */
                $token = $provider->getAccessToken('refresh_token', [
                    'refresh_token' => $GLOBALS['data']['refreshToken']
                ]);

                /** @var AccessToken $token */
                $user = $provider->getResourceOwner($token);
                $this->discordId = $user->getId();
            } catch (IdentityProviderException $ignored) {}
        }
    }

    /**
     * @param User $user
     */
    public function sendVerificationEmail(User $user) {
        /*if(strlen($user->email) > 0 && strlen($user->emailToken) > 0) {
            try {
                $mailFactory = new MailFactory();
                $mailFactory->setSubject('Eldrios - Please verify your email address')
                    ->setHtmlBody('This is the verification token: <a href="https://web.api.eldrios.com/user/' . $user->id . '/verify/' . $user->emailToken . '">' . $user->emailToken . '</a>')
                    ->setTextBody('Viewing this means that your email client doesn\'t support HTML emails.
                Which means you\'ll have to manually enter this code: ' . $user->emailToken)
                    ->setFromEmail('no-reply@eldrios.com')
                    ->setFromName('Eldrios')
                    ->send($user->email);
            } catch(\Exception $ex) {
                new Error($ex->getMessage());
            }
        } else {
            new Error('Failed to send verification email.');
        }*/
    }

    public function generateEmailToken() {
        $token = $this->generateRandomString(36);
        $this->emailToken = $token;
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}