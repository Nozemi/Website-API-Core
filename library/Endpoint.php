<?php namespace NozCore;

use NozCore\Message\Error;

abstract class Endpoint {

    protected $object = -1;
    protected $getByNameColumn = 'name';

    protected $result = [];
    protected $responseCode = 200;

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function get() {
        //new ActivityManager($this);

        $name = (isset($_REQUEST['name']) ? $_REQUEST['name'] : false);
        $id   = (isset($_REQUEST['id']) ? $_REQUEST['id'] : false);

        if($this->object != -1) {
            /** @var ObjectBase $object */
            $object = new $this->object();
            $object->setQueryLimit(isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 25);
            $object->setQueryPage(isset($_REQUEST['page']) ? $_REQUEST['page'] : 0);

            if($id) {
                $this->result = $object->get($id);
                return;
            }

            if(isset($_REQUEST['since']) || isset($_REQUEST['until'])) {
                $since = (isset($_REQUEST['since'])) ? $_REQUEST['since'] : null;
                $until = (isset($_REQUEST['until'])) ? $_REQUEST['until'] : date('Y-m-d H:i:s');
                $this->result = $object->getBetween($since, $until);
                return;
            }

            $filters = [];
            foreach($object->data() as $filter => $dataType) {
                if(isset($_REQUEST[$filter])) {
                    $filters[$filter] = DataTypes::parseValue($_REQUEST[$filter], $dataType);
                }
            }

            if((empty($filters) || (isset($filters['name']) && count($filters) == 1)) && $name) {
                $this->result = $object->getByName($name, $this->getByNameColumn);
                return;
            }

            if(empty($filters)) {
                $this->result = $object->getAll();
                return;
            }

            $this->result = $object->getByFilters($filters);
        } else {
            new Error('Endpoint not yet handling GET requests.');
        }
    }

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function put() {
        if($this->object != -1) {
            /** @var ObjectBase $object */
            $object = new $this->object($GLOBALS['data']);
            $object->setProperty('id', $_REQUEST['id']);
            $object = $object->save('PUT');

            $this->result = $object->get($_REQUEST['id']);
            return;
        }

        new Error('Endpoint not yet handling PUT requests.');
    }

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function post() {
        if($this->object != -1) {
            if (isset($GLOBALS['data']['id'])) {
                unset($GLOBALS['data']['id']);
            }

            /** @var ObjectBase $object */
            $object = new $this->object($GLOBALS['data']);
            $object = $object->save('POST');

            $this->result = $object;
            return;
        }

        new Error('Endpoint not yet handling POST requests.');
    }

    public function delete() {
        new Error('Endpoint not yet handling DELETE requests.');
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