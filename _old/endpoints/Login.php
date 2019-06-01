<?php namespace NozCore\Endpoints;

use NozCore\Authenticator;
use NozCore\Endpoint;

class Login extends Endpoint {

    public function post() {
        if(isset($_REQUEST['username'])) {
            $username = $_REQUEST['username'];
        }

        if(isset($_REQUEST['password'])) {
            $password = $_REQUEST['password'];
        }

        if(isset($password) && isset($username)) {
            $user = new \NozCore\Objects\User();
            $user->setProperty('username', $username);

            if($user->login($password)) {
                $user = $user->getUserByName($username);
                $auth = new Authenticator();
                $auth->authenticateUser($user);
                $this->responseCode = 200;
                return;
            }
        }

        $this->responseCode = 401;
    }
}