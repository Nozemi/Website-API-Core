<?php namespace NozCore\Objects;

use NozCore\DataTypes;
use NozCore\ObjectBase;

class SystemLog extends ObjectBase {

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {
        return [
            'id' => DataTypes::INTEGER,
            'userId' => DataTypes::INTEGER,
            'timestamp' => DataTypes::TIMESTAMP,
            'action' => DataTypes::STRING,
            'endpoint' => DataTypes::STRING,
            'message' => DataTypes::STRING
        ];
    }
}