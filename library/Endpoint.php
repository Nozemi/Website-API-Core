<?php namespace NozCore;

use NozCore\Message\Info;

abstract class Endpoint {

    protected $object = null;
    protected $getByNameColumn = 'name';

    protected $result = [];
    protected $responseCode = 200;

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function get() {
        $name = (isset($_REQUEST['name']) ? $_REQUEST['name'] : false);
        $id   = (isset($_REQUEST['id']) ? $_REQUEST['id'] : false);

        /** @var ObjectBase $group */
        $group = new $this->object();
        if($name) {
            $this->result = $group->getByName($name, $this->getByNameColumn);
        } else if($id) {
            $this->result = $group->get($id);
        } else {
            $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 100;
            $page = isset($_REQUEST['page']) ? ((intval($_REQUEST['page']) - 1) * $limit) : 0;

            $this->result = $group->getAll($limit, $page);
        }
    }

    public function put() {
        new Info('Endpoint not yet handling PUT requests.');
    }

    public function post() {
        new Info('Endpoint not yet handling POST requests.');
    }

    public function delete() {
        new Info('Endpoint not yet handling DELETE requests.');
    }

    /**
     * Endpoint constructor.
     * @param array $data
     * @throws \Exception
     */
    public function __construct($data = []) {
        if($this->object === null) {
            throw new \Exception('You need to specify an object in order to fetch data.');
        }
    }

    public function printResult($type = 'json') {
        http_response_code($this->responseCode);
        header("Content-Type: application/json");

        echo json_encode($this->result, JSON_PRETTY_PRINT);
    }
}