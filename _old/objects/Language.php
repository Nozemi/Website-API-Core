<?php namespace NozCore\Objects;

use NozCore\DataTypes;
use NozCore\ObjectBase;

class Language extends ObjectBase {

    protected $table = 'api_language';

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {
        return [
            'id' => DataTypes::INTEGER,
            'key' => DataTypes::STRING,
            'value' => DataTypes::STRING
        ];
    }
}