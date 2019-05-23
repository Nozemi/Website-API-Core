<?php namespace NozCore\Endpoints\UserAuthentication;

use NozCore\Authenticator;
use NozCore\Endpoint;
use NozCore\Objects\User;

class TokenLogin extends Endpoint {

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function post() {
        $token = false;

        if(isset($GLOBALS['data']['accessToken'])) {
            $token = $GLOBALS['data']['accessToken'];
        }

        if($token) {
            $auth = new Authenticator();
            if($auth->authenticateToken($token)) {
                $this->responseCode = 200;
                $user = new User();

                if(isset($_SESSION['user']['id'])) {
                    $user = $user->get($_SESSION['user']['id']);

                    $this->result = [
                        'id' => $_SESSION['user']['id'],
                        'groupId' => $_SESSION['user']['groupId'],
                        'username' => $user->getProperty('username')
                    ];
                    return;
                }
            }
        }

        $this->responseCode = 401;
    }
}