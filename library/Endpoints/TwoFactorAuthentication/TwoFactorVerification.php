<?php namespace NozCore\Endpoints\TwoFactorAuthentication;

use NozCore\Endpoint;
use NozCore\TwoFactorAuthentication;

class TwoFactorVerification extends Endpoint {

    protected $object = -1;

    public function post() {
        $key = $GLOBALS['config']->authyKey;
        $TwoFA = new TwoFactorAuthentication($key);

        $userId = $token = false;

        if(isset($GLOBALS['data']['userId'])) {
            $userId = intval($GLOBALS['data']['userId']);
        }

        if(isset($GLOBALS['data']['token'])) {
            $token = intval($GLOBALS['data']['token']);
        }

        if(!$token || !$userId) {
            $this->responseCode = 401;
            return;
        }

        //connectToDb('192.168.1.4', 'dodiannet', 'old_exorth', 'Logan11122!!');
        $db = $GLOBALS['hydra'];
        /** @var Table $table */
        $table = $db->table('api_user');
        $result = $table->select(['twoFAAccountId'])
            ->where('userid', $userId)
            ->one();

        if($TwoFA->verifyToken(intval($result['twoFAAccountId']), $token)) {
            $this->responseCode = 204;
        } else {
            $this->responseCode = 401;
        }

        $this->result = [
            'userId' => $userId,
            'result' => $result
        ];
    }
}