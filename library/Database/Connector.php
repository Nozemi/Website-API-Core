<?php namespace NozCore\Database;

interface Connector {

    public function __construct($database, $password = '', $username = 'root', $host = 'localhost', $port = 3306);
    public function connect();
}