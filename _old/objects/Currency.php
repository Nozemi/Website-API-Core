<?php namespace NozCore\Objects;

use NozCore\DataTypes;
use NozCore\ObjectBase;

class Currency extends ObjectBase {

    protected $table = 'api_currencies';

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {
        return [
            'id'            => DataTypes::INTEGER,
            'name'          => DataTypes::STRING,
            'short_name'    => DataTypes::STRING,
            'prefix'        => DataTypes::STRING,
            'suffix'        => DataTypes::STRING,
            'symbol'        => DataTypes::STRING
        ];
    }
}