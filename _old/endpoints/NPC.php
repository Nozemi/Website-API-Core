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
class NPC extends Endpoint {

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function get() {
        $item = new \NozCore\Objects\NPC();
        if($_REQUEST['endpoint'] == 'npcs' && isset($_REQUEST['name'])) {
            $this->result = $item->getByName($_REQUEST['name']);
        } else if($_REQUEST['endpoint'] == 'npcs') {
            $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 100;
            $page = isset($_REQUEST['page']) ? ((intval($_REQUEST['page']) - 1) * $limit) : 0;

            $this->result = $item->getAll($limit, $page);
        } else if($_REQUEST['endpoint'] == 'npc' && isset($_REQUEST['id'])) {
            $this->result = $item->get($_REQUEST['id']);
        }
    }

    /**
     * @throws \ReflectionException
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function put() {
        // TODO: Implement put() method.
        $item = new \NozCore\Objects\NPC();
        if(isset($_REQUEST['id'])) {
            $item = $item->get($_REQUEST['id']);
        }

        foreach($GLOBALS['data'] as $key => $value) {
            if (array_key_exists($key, $item->data()) && $item->getProperty($key) != $value) {
                $item->setProperty($key, $value);
            }
        }

        $this->result = $item->save();
    }

    /**
     * @throws \ReflectionException
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function post() {
        $item = new \NozCore\Objects\NPC($GLOBALS['data']);
        $this->result = $item->save();
    }

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function delete() {
        if(isset($_REQUEST['id'])) {
            $item = new \NozCore\Objects\NPC();

            if($item->get($_REQUEST['id'])) {
                $item->delete($_REQUEST['id']);
                $this->responseCode = 204;
            } else {
                new Info('There was no product with that ID.');
            }
        } else {
            new Error('You need to specify an ID in order to delete an object.');
        }
    }
}