<?php namespace NozCore\Objects;

use NozCore\DataTypes;
use NozCore\ObjectBase;

class Group extends ObjectBase {

    protected $table = 'group';

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {
        return [
            'id'          => DataTypes::INTEGER,
            'name'        => DataTypes::STRING,
            'description' => DataTypes::STRING,
            'full_access'  => DataTypes::BOOLEAN
        ];
    }
}