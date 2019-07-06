<?php namespace NozCore\Objects\Game;

use NozCore\DataTypes;
use NozCore\ObjectBase;

class Item extends ObjectBase {

    protected $table = 'game_item';

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {
        return [
            'id'                => DataTypes::INTEGER,
            'name'              => DataTypes::STRING,
            'examine'           => DataTypes::STRING,
            'weaponInterface'   => DataTypes::STRING,
            'equipmentType'     => DataTypes::STRING,
            'twoHanded'         => DataTypes::BOOLEAN,
            'stackable'         => DataTypes::BOOLEAN,
            'tradeable'         => DataTypes::BOOLEAN,
            'dropable'          => DataTypes::BOOLEAN,
            'sellable'          => DataTypes::BOOLEAN,
            'noted'             => DataTypes::BOOLEAN,
            'notedId'           => DataTypes::INTEGER,
            'value'             => DataTypes::INTEGER
        ];
    }
}