<?php namespace NozCore\Endpoints\UserAuthentication;

use NozCore\Authenticator;
use NozCore\Endpoint;
use NozCore\Objects\Users\User;

class TokenLogin extends Endpoint {

    protected $object = -1;

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
            $id = $auth->authenticateToken($token);

            if(!$id) {
                $this->responseCode = 401;
                $this->result = ['nope'];
                return;
            } else {
                $this->responseCode = 200;
                $user = new User();
                $user = $user->get($id);

                $this->result = $user->jsonSerialize();
                $this->result['accessToken'] = $token;
                return;
            }
        }

        $this->responseCode = 401;
    }
}