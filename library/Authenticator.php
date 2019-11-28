<?php namespace NozCore;

use ClanCats\Hydrahon\Builder;
use ClanCats\Hydrahon\Query\Sql\Table;
use NozCore\Objects\Users\User;

class Authenticator {

    /** @var Builder $db */
    private $db = null;
    /** @var \PDO $pdo */
    private $pdo = null;

    /** @var Table $dbTable */
    private $dbTable = null;

    public function __construct() {
        $this->db = $GLOBALS['hydra'];
        $this->dbTable = $this->db->table('api_token');
    }

    public function generateJWT($userId = 0) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);

        // Create token payload as a JSON string
        $payload = json_encode(['user_id' => $userId]);

        // Encode Header to Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, md5(time()) . 'noz_core.' . (isset($GLOBALS['config']->database->prefix) ? $GLOBALS['config']->database->prefix : '').'jwttoken.key', true);

        // Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        // Create JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        return $jwt;
    }

    private function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in _old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    /**
     * get access token from header
     * */
    public function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return false;
    }

    public function authenticateUser(User $user) {
        /*if($this->getBearerToken()) {
            $query = $this->dbTable->select()->where('token', $this->getBearerToken())->execute();
            return $query[0]['token'];
        } else {*/
            //$this->dbTable->delete()->where('userId', $user->getProperty('id'))->execute();
            $token = $this->generateJWT($user->getProperty('id'));

            $this->dbTable->insert([
                'userId'     => intval($user->getProperty('id')),
                'token'      => $token,
                'created'    => date('Y-m-d H:i:s'),
                'expiration' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +1 month')),
                'client'     => ''
            ])->execute();

            $_SESSION['user'] = [
                'id' => $user->getProperty('id'),
                'groupId' => $user->getProperty('groupId'),
                'token' => $token
            ];

            return $token;
        //}
    }

    /**
     * @param $token
     * @return bool|mixed|null
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function authenticateToken($token) {
        $query = $this->dbTable->select()->where('token', $token)->execute();

        if(!empty($query)) {
            $userId = $query[0]['userId'];
            $user = new User();
            $user = $user->get($userId);

            $_SESSION['user'] = [
                'id' => $user->getProperty('id'),
                'groupId' => $user->getProperty('groupId'),
                'token' => $token
            ];

            return $user->getProperty('id');
        } else {
            unset($_SESSION['user']);
        }

        return false;
    }
}