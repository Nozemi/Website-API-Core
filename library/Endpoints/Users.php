<?php namespace NozCore\Endpoints;

use NozCore\Endpoint;

class Users extends Endpoint {
    protected $object = 'NozCore\Objects\Users\User';
    protected $getByNameColumn = 'username';
}