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
class Product extends Endpoint {

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function get() {
        $product = new \NozCore\Objects\Product();
        if($_REQUEST['endpoint'] == 'products') {
            $this->result = $product->getAll();
        } else if($_REQUEST['endpoint'] == 'product' && isset($_REQUEST['id'])) {
            $this->result = $product->get($_REQUEST['id']);
        }
    }

    /**
     * @throws \ReflectionException
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function put() {
        // TODO: Implement put() method.
        $product = new \NozCore\Objects\Product();
        if(isset($_REQUEST['id'])) {
            $product = $product->get($_REQUEST['id']);
        }

        foreach($GLOBALS['data'] as $key => $value) {
            if (array_key_exists($key, $product->data()) && $product->getProperty($key) != $value) {
                $product->setProperty($key, $value);
            }
        }

        $this->result = $product->save();
    }

    /**
     * @throws \ReflectionException
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function post() {
        $product = new \NozCore\Objects\Product($GLOBALS['data']);
        $this->result = $product->save();
    }

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function delete() {
        if(isset($_REQUEST['id'])) {
            $product = new \NozCore\Objects\Product();

            if($product->get($_REQUEST['id'])) {
                $product->delete($_REQUEST['id']);
                $this->responseCode = 204;
            } else {
                new Info('There was no product with that ID.');
            }
        } else {
            new Error('You need to specify an ID in order to delete an object.');
        }
    }
}