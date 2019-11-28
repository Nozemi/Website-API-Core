<?php namespace NozCore;

use Authy\AuthyApi;

class TwoFactorAuthentication {

    /** @var AuthyApi $authy */
    private $authy;

    public function __construct($apiKey) {
        $this->authy = new AuthyApi($apiKey);
    }

    /**
     * @param $userId
     * @param $token
     * @param bool $force
     * @return bool
     */
    public function verifyToken($userId, $token, $force = false) {
        if(!is_int($userId) || !is_int($token)) {
            //return false;
        }

        if(strlen((string)$token) < 6 || strlen((string)$token) > 8) {
            //return false;
        }

        $verification = $this->authy->verifyToken($userId, $token, ['force' => $force]);
        if($verification->ok()) {
            return true;
        }

        return false;
    }

    /**
     * @param $email
     * @param $phone
     * @param $countryCode
     * @return bool
     */
    public function registerUser($email, $phone, $countryCode) {
        $user = $this->authy->registerUser($email, $phone, $countryCode);
        if($user->ok()) {
            return true;
        }

        return false;
    }
}