<?php namespace NozCore\Objects;

use NozCore\DataTypes;
use NozCore\ObjectBase;

class ActiveUser extends ObjectBase {

    protected $table = 'api_active_users';
    protected $defaultSort = 'lastActive';
    protected $defaultSortOrder = 'desc';
    protected $betweenColumn = 'lastActive';

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {

        return [
            'id'            => DataTypes::STRING,
            'userId'        => DataTypes::INTEGER,
            'lastActive'    => DataTypes::TIMESTAMP,
            'viewing'       => DataTypes::STRING,
            'userAgent'     => DataTypes::STRING,
            'ip'            => DataTypes::STRING
        ];
    }
}