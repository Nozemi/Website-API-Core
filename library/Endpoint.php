<?php namespace NozCore;

use NozCore\Message\Info;

abstract class Endpoint {

    protected $result = [];
    protected $responseCode = 200;

    public function get() {
        new Info('Endpoint not yet handling GET requests.');
    }

    public function put() {
        new Info('Endpoint not yet handling PUT requests.');
    }

    public function post() {
        new Info('Endpoint not yet handling POST requests.');
    }

    public function delete() {
        new Info('Endpoint not yet handling DELETE requests.');
    }

    /**
     * Endpoint constructor.
     * @param array $data
     */
    public function __construct($data = []) {
    }

    public function printResult($type = 'json') {
        http_response_code($this->responseCode);
        header("Content-Type: application/json");

        echo json_encode($this->result, JSON_PRETTY_PRINT);
    }
}