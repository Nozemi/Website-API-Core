<?php namespace NozCore\Objects;

use NozCore\DataTypes;
use NozCore\ObjectBase;

/**
 * Class Product
 *
 * @property int id
 * @property String name
 * @property String description
 * @property int priceId
 * @property int categoryId
 * @property ProductPrice price
 *
 * @package NozCore\Objects
 *
 */
class Item extends ObjectBase {

    protected $table = 'items';

    public function data() {

        return [
            'id'   => DataTypes::INTEGER,
            'name' => DataTypes::STRING,
            'examine' => DataTypes::STRING,
            'value' => DataTypes::INTEGER,
            'equipmentType' => DataTypes::STRING,
            'tradeable' => DataTypes::BOOLEAN,
            'dropable' => DataTypes::BOOLEAN,
            'sellable' => DataTypes::BOOLEAN,
            'doubleHanded' => DataTypes::BOOLEAN,
            'weight' => DataTypes::DOUBLE
        ];
    }
}