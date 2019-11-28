<?php namespace NozCore\Objects\Game;

use NozCore\DataTypes;
use NozCore\ObjectBase;

class NPC extends ObjectBase {

    protected $table = 'game_npc';

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {
        return [
            'id'            => DataTypes::INTEGER,
            'name'          => DataTypes::STRING,
            'examine'       => DataTypes::STRING,
            'size'          => DataTypes::INTEGER,
            'walkRadius'    => DataTypes::INTEGER,
            'attackable'    => DataTypes::BOOLEAN,
            'retreats'      => DataTypes::BOOLEAN,
            'aggressive'    => DataTypes::BOOLEAN,
            'poisonous'     => DataTypes::BOOLEAN,
            'respawn'       => DataTypes::INTEGER,
            'maxHit'        => DataTypes::INTEGER,
            'hitpoints'     => DataTypes::INTEGER,
            'attackSpeed'   => DataTypes::INTEGER,
            'combatLevel'   => DataTypes::INTEGER
        ];
    }
}