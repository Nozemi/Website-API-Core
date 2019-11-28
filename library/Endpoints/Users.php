<?php namespace NozCore\Endpoints;

use NozCore\Endpoint;
use NozCore\Message\Error;
use NozCore\Message\Info;
use NozCore\Objects\Users\User;

class Users extends Endpoint {
    protected $object = 'NozCore\Objects\Users\User';
    protected $getByNameColumn = 'username';

    public function get() {
        if(isset($_REQUEST['emailToken']) && strlen($_REQUEST['emailToken']) > 0) {
            if(isset($_REQUEST['id'])) {
                $user = new User();
                $user = $user->get($_REQUEST['id']);

                if($user != null && $_REQUEST['emailToken'] == $user->getProperty('emailToken')) {
                    $user->setProperty('verified', 1);
                    $user->setProperty('emailToken', '');
                    $user->save('SERVER');
                    new Info('Successfully verified email.');
                } else {
                    new Error('Failed to verify email.');
                }
            }
        } else {
            parent::get();
        }
    }
}