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
class Group extends Endpoint {

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function get() {
        $group = new \NozCore\Objects\Group();
        if($_REQUEST['endpoint'] == 'groups' && isset($_REQUEST['name'])) {
            $this->result = $group->getByName($_REQUEST['name']);
        } else if($_REQUEST['endpoint'] == 'groups') {
            $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 100;
            $page = isset($_REQUEST['page']) ? ((intval($_REQUEST['page']) - 1) * $limit) : 0;

            $this->result = $group->getAll($limit, $page);
        } else if($_REQUEST['endpoint'] == 'group' && isset($_REQUEST['id'])) {
            $this->result = $group->get($_REQUEST['id']);
        }
    }

    /**
     * @throws \ReflectionException
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function put() {
        // TODO: Implement put() method.
        $group = new \NozCore\Objects\Group();
        if(isset($_REQUEST['id'])) {
            $group = $group->get($_REQUEST['id']);
        }

        foreach($GLOBALS['data'] as $key => $value) {
            if (array_key_exists($key, $group->data()) && $group->getProperty($key) != $value) {
                $group->setProperty($key, $value);
            }
        }

        $this->result = $group->save();
    }

    /**
     * @throws \ReflectionException
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function post() {
        $group = new \NozCore\Objects\Group($GLOBALS['data']);
        $this->result = $group->save();
    }

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function delete() {
        if(isset($_REQUEST['id'])) {
            $user = new \NozCore\Objects\NPC();

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