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
class ProductCategory extends Endpoint {

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function get() {
        $product = new \NozCore\Objects\ProductCategory();
        if($_REQUEST['endpoint'] == 'product-categories') {
            $this->result = $product->getAll();
        } else if($_REQUEST['endpoint'] == 'product-category' && $_REQUEST['id']) {
            $this->result = $product->get($_REQUEST['id']);
        }
    }

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function put() {
        // TODO: Implement put() method.
        $product = new \NozCore\Objects\ProductCategory();
        if(isset($_REQUEST['categoryId'])) {
            $product = $product->get($_REQUEST['categoryId']);
        }

        foreach($GLOBALS['data'] as $key => $value) {
            if (array_key_exists($key, $product->data()) && $product->getProperty($key) != $value) {
                $product->setProperty($key, $value);
            }
        }

        $this->result = $product->save();
    }

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function post() {
        $product = new \NozCore\Objects\ProductCategory($GLOBALS['data']);
        $this->result = $product->save();
    }

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function delete() {
        if(isset($_REQUEST['id'])) {
            $product = new \NozCore\Objects\ProductCategory();

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

    public function process() {
        // TODO: Implement process() method.
    }
}