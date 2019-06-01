<?php

use NozCore\Authenticator;
use NozCore\Endpoint;
use NozCore\Message\Error;
use NozCore\Validator;

if(empty($_SESSION)) {
    session_start();
}

$http_origin = (isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*');

$config = [
    'allowedOrigins' => [
        'http://localhost',
        'https://localhost',
        'http://dodian.net',
        'https://dodian.net'
    ],
    'fileRoot' => '::SITEROOT::/files/',
    'authyKey' => false,
    'database' => [
        'username' => 'root',
        'password' => '',
        'database' => 'rsps_dev',
        'host'     => 'localhost',
        'port'     => 3306,
        'prefix'   => ''
    ]
];

$configFile = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
foreach($configFile as $property => $value) {
    if(is_array($value)) {
        foreach($value as $property2 => $value2) {
            $config[$property][$property2] = $value2;
        }
    } else {
        $config[$property] = $value;
    }
}
$config = (object) $config;

$GLOBALS['config'] = $config;
$GLOBALS['rootDir'] = __DIR__;

$allowedOrigins = $config->allowedOrigins;
if(!in_array($http_origin, $allowedOrigins)) {
    $http_origin = '*';
}

$allowCredentials = 'true';
if($http_origin == '*') {
    $allowCredentials = 'false';
}

header("Access-Control-Allow-Origin: {$http_origin}");
header("Access-Control-Allow-Credentials: {$allowCredentials}");

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit; // OPTIONS request wants only the policy, we can stop here
}

require('./global.php');
require('./vendor/autoload.php');

if(!isset($_REQUEST['endpoint'])) {
    new Error('No endpoint was specified.');
}

$GLOBALS['data'] = [];
if(is_array(json_decode(file_get_contents('php://input'), true))) {
    $GLOBALS['data'] = json_decode(file_get_contents('php://input'), true);
}

function connectToDb($host, $name, $user, $pass, $port = 3306) {
    $GLOBALS['pdo'] = new PDO('mysql:host=' . $host . ':' . $port . ';dbname=' . $name . ';charset=utf8', $user, $pass);

    $pdoConnection = $GLOBALS['pdo'];
    $GLOBALS['hydra']  = new \ClanCats\Hydrahon\Builder('mysql', function($query, $queryString, $queryParameters) use($pdoConnection) {
        $statement = $pdoConnection->prepare($queryString);
        $statement->execute($queryParameters);

        if($query instanceof \ClanCats\Hydrahon\Query\Sql\FetchableInterface) {
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        }

        return true;
    });
}

connectToDb('localhost', 'rsps_dev', 'rsps', $config->database['password']);

$auth = new Authenticator();
$token = $auth->getBearerToken();
$auth->authenticateToken($token);

// We need to map each endpoint in here so we know which class to use.
$endpointMap = json_decode(file_get_contents(__DIR__ . '/endpoints.json'), true);

// We need to know which type of request it is,
// whether it is a GET, POST, PUT or DELETE request.
$method = $_SERVER['REQUEST_METHOD'];
// Then we check if the currently requested endpoint is mapped.
if(array_key_exists($_REQUEST['endpoint'], $endpointMap)) {
    // We want to get the mapped class name of the requested endpoint.
    $endpoint = $endpointMap[$_REQUEST['endpoint']];
    // We want to check if that endpoint class actually exists.
    if(class_exists($endpoint)) {
        /** @var Endpoint $endpointClass */
        $endpointClass = new $endpoint();

        $validator = new Validator();
        if($validator->validateEndpoint($endpointClass)) {
            $method = strtolower($method);

            $endpointClass->$method();
            $endpointClass->printResult();
        } else {
            new Error('Somehow the endpoint class was invalid.');
        }
    } else {
        new Error('Endpoint ' . $_REQUEST['endpoint'] . ' not found.');
    }
} else {
    // If endpoint isn't mapped, we'll return this error message.
    new Error('Endpoint ' . $_REQUEST['endpoint'] . ' not mapped to an endpoint class.');
}