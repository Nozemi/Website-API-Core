<?php namespace NozCore;

use ClanCats\Hydrahon\Builder;
use ClanCats\Hydrahon\Query\Sql\Table;
use NozCore\Objects\Group;
use NozCore\Objects\Users\User;

class Permissions {

    /** @var Builder $db */
    private $db = null;
    /** @var \PDO $pdo */
    private $pdo = null;

    public function __construct() {
        $this->db = $GLOBALS['hydra'];
        $this->pdo = $GLOBALS['pdo'];
    }

    public function groupHasAccess(Group $group, $permission) {
        if($group->getProperty('fullAccess')) {
            return true;
        }

        $query = $this->db->from('api_permissions')->where('groupId', $group->getProperty('id'))
            ->where('key', $permission);
        print_r($query[0]);

        return false;
    }

    /**
     * @param $key
     * @return bool
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function checkPermissions($key) {
        if(isset($_SESSION['user']['userId'])) {
            $user = new User();
            $user = $user->get($_SESSION['user']['userId']);

            if($user->getProperty('fullAccess')) {
                return true;
            }

            if($user->getProperty('groupId')) {
                $group = new Group();
                /** @var Group $group */
                $group = $group->get($user->getProperty('groupId'));

                if($this->groupHasAccess($group, $key)) {
                    return true;
                }
            }
        }

        if($this->guestAccess($key)) {
            return true;
        }

        return false;
    }

    /**
     * @param $key
     * @return bool
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function guestAccess($key) {
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        /** @var Table $guestPerms */
        $guestPerms = $this->db->table('api_permissions');
        $result = $guestPerms->select()
            ->where('key', $key)
            ->andWhere('groupId', -1)
            ->one();

        if(isset($result[$method]) && $result[$method]) {
            return true;
        }

        return true;
    }
}