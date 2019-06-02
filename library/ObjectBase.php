<?php namespace NozCore;

use ClanCats\Hydrahon\Builder;
use ClanCats\Hydrahon\Query\Sql\Table;
use JsonSerializable;
use NozCore\Message\Error;

abstract class ObjectBase implements JsonSerializable {

    /** @var Builder $db */
    protected $db = null;
    /** @var \PDO $pdo */
    protected $pdo = null;

    protected $table = '';
    protected $hooks = [];

    /** @var Table $dbTable */
    protected $dbTable = null;

    protected $permissions = [];

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    abstract public function data();

    /**
     * ObjectBase constructor.
     * @param array $data
     */
    public function __construct($data = []) {
        $this->db  = $GLOBALS['hydra'];
        $this->pdo = $GLOBALS['pdo'];

        $this->dbTable = $this->db->table($this->table);

        foreach($this->data() as $property => $type) {
            if(isset($data[$property])) {
                $this->$property = DataTypes::parseValue($data[$property], $type);
            }
        }
    }

    /**
     * Custom serializer for objects.
     *
     * @return array
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function jsonSerialize() {
        $this->permissions();

        $dataToSerialize = [];
        foreach($this->data() as $property => $type) {
            if($this->getPermission($property)) {
                if (isset($this->$property)) {
                    $dataToSerialize[$property] = $this->$property;
                }
            }
        }

        return $dataToSerialize;
    }

    /**
     * Get all entries from the selected database
     *
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \ReflectionException
     */
    public function getAll($limit = 0, $offset = 0) {
        $objects = [];

        /** @var Table $table */
        $query = $this->dbTable->select()
            ->limit($limit)
            ->offset($offset)
            ->execute();

        foreach($query as $row) {
            $object = new $this($row);
            $this->callHooks('SUCCESSFUL_GET_EVENT', $object);
            $objects[] = $object;
        }

        return $objects;
    }

    /**
     * @param $name
     * @param string $column
     * @return array
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function getByName($name, $column = 'name') {
        $objects = [];

        $this->callHooks('BEFORE_GET_EVENT');
        $this->callHooks('BEFORE_GET_BY_NAME_EVENT');

        $query = $this->dbTable->select()
            ->where($column, 'LIKE', '%'.$name.'%')
            ->execute();

        foreach($query as $row) {
            $object = new $this($row);
            $this->callHooks('SUCCESSFUL_GET_EVENT', $object);
            $this->callHooks('SUCCESSFUL_GET_BY_NAME_EVENT', $object);
            $objects[] = $object;
        }

        return $objects;
    }

    /**
     * @param $id
     * @return ObjectBase
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function get($id) {
        $this->callHooks('BEFORE_GET_EVENT');

        $result = $this->dbTable->select()
            ->where('id', $id)
            ->one();

        if(!empty($result)) {
            $object = new $this($result);
            $this->callHooks('SUCCESSFUL_GET_EVENT', $object);
            return $object;
        }

        return null;
    }

    /**
     * @param string $method
     * @return ObjectBase
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function save($method = 'POST') {
        $this->callHooks('BEFORE_SAVE_EVENT');

        if($this->getProperty('id')) {
            $this->callHooks('BEFORE_SAVE_WITH_ID_EVENT');
        } else {
            $this->callHooks('BEFORE_SAVE_WITHOUT_ID_EVENT');
        }

        $this->permissions($method);

        $dataToSerialize = [];
        foreach($this->data() as $property => $type) {
            if(isset($this->$property) && $this->getPermission($property)) {
                $dataToSerialize[$property] = $this->$property;
            }
        }

        if($this->getProperty('id')/* && !$this instanceof File*/) {
            // Update object
            $this->callHooks('BEFORE_SAVE_EXISTING_EVENT');
            $this->dbTable->update($dataToSerialize)
                ->where('id', $this->getProperty('id'))
                ->execute();
            $objectId = $this->getProperty('id');
            $this->callHooks('AFTER_SAVE_EXISTING_EVENT');
        } else {
            // Create object
            $this->callHooks('BEFORE_SAVE_NEW_EVENT');
            $this->dbTable->insert($dataToSerialize)->execute();

            $objectId = $this->pdo->lastInsertId();
            $this->callHooks('AFTER_SAVE_NEW_EVENT', $this->get($objectId));
        }

        return $this->get($objectId);
    }

    /**
     * @param $id
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function delete($id) {
        $this->dbTable->delete()->where('id', $id)->execute();
    }

    /**
     * Get a property from the object.
     *
     * @param $property
     * @return bool|mixed|null
     */
    public function getProperty($property) {
        if(array_key_exists($property, $this->data()) && isset($this->$property)) {
            return DataTypes::parseValue($this->$property, $this->data()[$property]);
        }

        return false;
    }

    /**
     * Set a property for the object.
     * If it succeeds, it will return the value you set. Otherwise it will return false.
     *
     * @param $property
     * @param $value
     * @return bool
     */
    public function setProperty($property, $value) {
        if(array_key_exists($property, $this->data())) {
            $this->$property = DataTypes::parseValue($value, $this->data()[$property]);
            return $this->$property;
        }

        return false;
    }

    /**
     * @param $hook
     * @param null $object
     * @throws \ReflectionException
     */
    public function callHooks($hook, $object = null) {
        if(isset($this->hooks[$hook])) {
            foreach($this->hooks[$hook] as $methodName) {
                if(method_exists($this, $methodName)) {
                    $method = new \ReflectionMethod($this, $methodName);
                    if($method->getNumberOfRequiredParameters() == 1) {
                        $this->$methodName($object);
                    } else {
                        $this->$methodName();
                    }
                }
            }
        }
    }

    /**
     * @param string $method
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function permissions($method = 'GET') {
        if($method == 'SERVER') {
            foreach($this->data() as $property => $type) {
                $this->permissions[$property] = true;
            }
        } else {
            /** @var Table $table */
            $table = $this->db->table('permissions');

            $groups = [0];
            if(isset($_SESSION['user'])) {
                $groupId = $_SESSION['user']['groupId'];
                $groups[] = $groupId;

                // TODO: Add a function to get inheritance from the player group
            }

            $result = $table->select()
                ->where('groupId', 'in', $groups)
                ->andWhere('table', $this->table)
                ->andWhere('method', $method)
                ->execute();

            foreach($result as $item) {
                $this->permissions[$item['key']] = boolval($item['value']);
            }
        }
    }

    public function getPermission($key) {
        if(isset($this->permissions[$key])) {
            return $this->permissions[$key];
        }

        return false;
    }
}