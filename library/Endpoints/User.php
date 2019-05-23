<?php namespace NozCore\Endpoints;

use NozCore\Endpoint;
use NozCore\Message\Error;
use NozCore\Message\Info;

/**
 * Class Product
 *
 * @property int id
 * @property String name
 * @property String description
 * @property Object price
 *
 * @package NozCore\Endpoints
 */
class User extends Endpoint {

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function get() {
        $user = new \NozCore\Objects\User();
        if($_REQUEST['endpoint'] == 'users' && isset($_REQUEST['name'])) {
            $this->result = $user->getByName($_REQUEST['name']);
        } else if($_REQUEST['endpoint'] == 'users') {
            $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 100;
            $page = isset($_REQUEST['page']) ? ((intval($_REQUEST['page']) - 1) * $limit) : 0;

            $this->result = $user->getAll($limit, $page);
        } else if($_REQUEST['endpoint'] == 'user' && isset($_REQUEST['id'])) {
            $this->result = $user->get($_REQUEST['id']);
        }
    }

    /**
     * @throws \ReflectionException
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function put() {
        // TODO: Implement put() method.
        $user = new \NozCore\Objects\User();
        if(isset($_REQUEST['id'])) {
            $user = $user->get($_REQUEST['id']);
        }

        foreach($GLOBALS['data'] as $key => $value) {
            if (array_key_exists($key, $user->data()) && $user->getProperty($key) != $value) {
                $user->setProperty($key, $value);
            }
        }

        $this->result = $user->save();
    }

    /**
     * @throws \ReflectionException
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function post() {
        $user = new \NozCore\Objects\User($GLOBALS['data']);
        $this->result = $user->save();
    }

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function delete() {
        if(isset($_REQUEST['id'])) {
            $user = new \NozCore\Objects\User();

            if($user->get($_REQUEST['id'])) {
                $user->delete($_REQUEST['id']);
                $this->responseCode = 204;
            } else {
                new Info('There was no product with that ID.');
            }
        } else {
            new Error('You need to specify an ID in order to delete an object.');
        }
    }
}