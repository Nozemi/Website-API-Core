<?php namespace NozCore\Objects\Users;

use ClanCats\Hydrahon\Builder;
use NozCore\DataTypes;
use NozCore\Message\Error;
use NozCore\ObjectBase;

class User extends ObjectBase {

    protected $table = 'user';

    protected $hooks = [
        'BEFORE_SAVE_EVENT' => [
            'hashPassword',
            'validateUsername',
            'validateEmail'
        ]
    ];

    protected $access = [
        'get' => [
            GUEST_ACCESS => ['id', 'username', 'groupId'],
            ADMIN_ACCESS => ['id', 'username', 'groupId', 'email', 'password']
        ],
        'post' => [
            GUEST_ACCESS => [],

        ]
    ];

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {
        return [
            'id'           => DataTypes::INTEGER,
            'username'     => DataTypes::STRING,
            'password'     => DataTypes::STRING,
            'email'        => DataTypes::STRING,
            'groupId'      => DataTypes::INTEGER,
            'twoFAEnabled' => DataTypes::BOOLEAN,
            'authyId'      => DataTypes::INTEGER,
            'registered'   => DataTypes::TIMESTAMP,
            'lastVisit'    => DataTypes::TIMESTAMP,
            'discordId'    => DataTypes::STRING
        ];
    }

    public function hashPassword() {
        if(isset($this->password)) {
            $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        }
    }

    public function validateUsername() {
        if(isset($this->username)) {
            $query = $this->db->from($this->table)->where('username', $this->username);
            if(!empty($query)) {
                new Error('Username is already taken.');
            }
        } else {
            new Error('Please provide a username in order to register.');
        }
    }

    public function validateEmail() {
        if(isset($this->email)) {
            if(filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $query = $this->db->from($this->table)->where('email', $this->email);
                if(!empty($query)) {
                    new Error('Email is already taken.');
                }
            } else {
                new Error('Please provide a valid email address.');
            }
        } else {
            new Error('Please provide an email address in order to register.');
        }
    }

    /**
     * @param bool $username
     * @return User|null
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function getUserByName($username = false) {
        if(isset($this->username) && !$username) {
            $username = $this->username;
        }

        if(!$username) {
            new Error('You need to specify a username.');
        }

        if($this->db instanceof Builder) {
            $query = $this->dbTable->select()->where('username', $username)->execute();
            return new User(end($query));
        }
        return null;
    }

    /**
     * @param $username
     * @param $password
     * @return bool|User|null
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function login($username, $password) {
        if(isset($this->username)) {
            $user = $this->getUserByName($username);

            if($user instanceof User && isset($user->password) && password_verify($password, $user->password)) {
                return true;
            }

            return $user;
        }

        return false;
    }
}