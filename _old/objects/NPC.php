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
class NPC extends ObjectBase {

    protected $table = 'npc';

    public function data() {

        return [
            'id'   => DataTypes::INTEGER,
            'name' => DataTypes::STRING,
            'examine' => DataTypes::STRING
        ];
    }
}