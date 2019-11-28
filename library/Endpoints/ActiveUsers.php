<?php namespace NozCore\Endpoints;

use NozCore\Endpoint;

class ActiveUsers extends Endpoint {
    protected $object = 'NozCore\Objects\ActiveUser';

    public function get() {
        if(!isset($_REQUEST['since'])) {
            $_REQUEST['since'] = date('Y-m-d H:i:s', time() - 600);
        }

        parent::get();
    }
}