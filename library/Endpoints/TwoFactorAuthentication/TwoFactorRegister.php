<?php namespace NozCore\Endpoints\TwoFactorAuthentication;

use NozCore\Endpoint;
use NozCore\TwoFactorAuthentication;

class TwoFactorRegister extends Endpoint {

    protected $object = -1;

    public function post() {
        $key = $GLOBALS['config']->authyKey;
        $TwoFA = new TwoFactorAuthentication($key);

        $email = $phone = $countryCode = false;

        if(isset($GLOBALS['data']['email'])) {
            $email = $GLOBALS['data']['email'];
        }

        if(isset($GLOBALS['data']['phone'])) {
            $phone = $GLOBALS['data']['phone'];
        }

        if(isset($GLOBALS['data']['code'])) {
            $countryCode = intval($GLOBALS['data']['code']);
        }

        if(!$email || !$phone || !$countryCode) {
            $this->responseCode = 500;
            return;
        }

        if($TwoFA->registerUser($email, $phone, $countryCode)) {
            $this->responseCode = 204;
        } else {
            $this->responseCode = 500;
        }
    }
}