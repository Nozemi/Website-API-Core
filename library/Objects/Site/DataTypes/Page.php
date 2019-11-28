<?php namespace NozCore\Objects\Site\DataTypes;

use NozCore\DataTypes;
use NozCore\ObjectBase;

class Page extends ObjectBase {

    protected $table = 'api_page';

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
            'template'      => DataTypes::STRING,
            'elements'      => DataTypes::JSON
        ];
    }
}