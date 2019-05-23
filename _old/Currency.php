<?php namespace NozCore\Endpoints;

use NozCore\Endpoint;
use NozCore\Message\Error;
use NozCore\Message\Info;

class Currency extends Endpoint {

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function get() {
        $currency = new \NozCore\Objects\Currency();
        if($_REQUEST['endpoint'] == 'currencies') {
            $this->result = $currency->getAll();
        } else if($_REQUEST['endpoint'] == 'currency' && isset($_REQUEST['id'])) {
            $this->result = $currency->get($_REQUEST['id']);
        }
    }

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function put() {
        $currency = new \NozCore\Objects\Currency();
        if(isset($_REQUEST['id'])) {
            $currency = $currency->get($_REQUEST['id']);
        }

        foreach($GLOBALS['data'] as $key => $value) {
            if(array_key_exists($key, $currency->data()) && $currency->getProperty($key) != $value) {
                $currency->setProperty($key, $value);
            }
        }

        $this->result = $currency->save();
    }

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function post() {
        $currency = new \NozCore\Objects\Currency($GLOBALS['data']);
        $this->result = $currency->save();
    }

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function delete() {
        if(isset($_REQUEST['id'])) {
            $currency = new \NozCore\Objects\Currency();

            if($currency->get($_REQUEST['id'])) {
                $currency->delete($_REQUEST['id']);
                $this->responseCode = 204;
            } else {
                new Info('There was no currency with that ID.');
            }
        } else {
            new Error('You need to specify an ID in order to delete an object.');
        }
    }
}