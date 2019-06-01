<?php namespace NozCore\Endpoints\UserAuthentication;

use NozCore\Authenticator;
use NozCore\Endpoint;
use NozCore\Objects\Users\User;
use NozCore\TwoFactorAuthentication;

class UserLogin extends Endpoint {

    protected $object = -1;

    public function post() {
        $username = $password = $token = false;

        if(isset($GLOBALS['data']['username'])) {
            $username = $GLOBALS['data']['username'];
        }

        if(isset($GLOBALS['data']['password'])) {
            $password = $GLOBALS['data']['password'];
        }

        if(isset($GLOBALS['data']['token'])) {
            $token = $GLOBALS['data']['token'];
        }

        if($username && $password) {
            $user = new User();
            $user->setProperty('username', $username);

            $result = $user->login($username, $password);

            if($result === true) {
                $user = $user->getUserByName($username);
                $auth = new Authenticator();
                $accessToken = $auth->authenticateUser($user);

                if($user->getProperty('twoFAEnabled')) {
                    $authy = new TwoFactorAuthentication($GLOBALS['config']->authyKey);
                    if(!$authy->verifyToken(intval($user->authyId), intval($token))) {
                        $this->responseCode = 401;
                        return;
                    }
                }

                $this->responseCode = 200;
                $this->result = [
                    'id' => $user->getProperty('id'),
                    'groupId' => $user->getProperty('groupId'),
                    'username' => $user->getProperty('username'),
                    'accessToken' => $accessToken,
                    //'user' => $user
                ];
                return;
            }
        }

        $this->responseCode = 401;
    }
}