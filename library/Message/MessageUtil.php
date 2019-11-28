<?php namespace NozCore\Message;

abstract class MessageUtil {

    private $message = null;
    protected $properties = [];

    public function __construct($message) {
        $this->message = $message;

        $this->output();
    }

    public function properties() {
        return [];
    }

    public function output() {
        $type = 200;
        if($this instanceof Error) {
            $type = 503;
        } else if($this instanceof AccessDenied) {
            $type = 403;
        }

        http_response_code($type);
        header('Content-Type: application/json');

        $this->properties = [
            'response_code' => $type,
            'message' => $this->message
        ];

        $this->properties = array_merge($this->properties, $this->properties());

        echo json_encode($this->properties);
        die();
    }
}