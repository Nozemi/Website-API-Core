<?php namespace NozCore\Endpoints;

use ClanCats\Hydrahon\Query\Sql\Table;
use NozCore\Endpoint;
use NozCore\TwoFactorAuthentication;

class GameLogin extends Endpoint {

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function post() {
        //connectToDb('192.168.1.4', 'dodiannet', 'old_exorth', 'Logan11122!!');

        $db  = $GLOBALS['hydra'];

        /** @var Table $table */
        $table = $db->table('api_user');

        $result = $table->select(['username', 'password', 'salt', 'userid', 'enabledTwoFA', 'email', 'twoFAAccountId'])
            ->where('username', $GLOBALS['data']['username'])
            ->one();

        $username = $result['username'];
        $password = $result['password'];
        $salt     = $result['salt'];
        $userId   = $result['userid'];
        $enabled  = boolval($result['enabledTwoFA']);
        $token    = $GLOBALS['data']['twoFactorToken'];

        $loginSuccess = false;
        if(md5(md5($GLOBALS['data']['password']) . $salt) == $password) {
            $loginSuccess = true;
        }

        if(!$loginSuccess) {
            $this->responseCode = 401;
            $this->result = [
                'message' => 'Incorrect username or password.'
            ];
            return;
        }

        if($enabled) {
            $key = $GLOBALS['config']->authyKey;
            $authy = new TwoFactorAuthentication($key);

            if($authy->verifyToken(intval($result['twoFAAccountId']), intval($token))) {
                $this->responseCode = 200;
                $this->result = [
                    'id' => intval($userId),
                    'username' => $username,
                    'password' => '',
                    'email' => $result['email'],
                    'lastLogin' => '',
                    'ipAddress' => '',
                    'twoFactorEnabled' => $enabled,
                    'accessToken' => 'NOT_IMPLEMENTED'
                ];
                return;
            } else {
                $this->responseCode = 401;
                $this->result = [
                    'message' => 'Two factor authentication failed.',
                    'token' => $token,
                    'userId' => intval($result['twoFAAccountId'])
                ];
                return;
            }
        }

        $this->responseCode = 200;
        $this->result = [
            'id' => $userId,
            'username' => $username,
            'password' => '',
            'email' => $result['email'],
            'lastLogin' => '',
            'ipAddress' => '',
            'twoFactorEnabled' => $enabled,
            'accessToken' => 'NOT_IMPLEMENTED'
        ];
    }
}