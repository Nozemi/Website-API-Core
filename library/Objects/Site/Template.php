<?php namespace NozCore\Objects\Site;

use NozCore\DataTypes;
use NozCore\ObjectBase;

class Template extends ObjectBase {

    protected $table = 'api_template';

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {

        return [
            'id'            => DataTypes::INTEGER,
            'name'          => DataTypes::STRING,
            'identifier'    => DataTypes::STRING,
            'html'          => DataTypes::STRING,
            'placeholders'  => DataTypes::JSON
        ];
    }
}