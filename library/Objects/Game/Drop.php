<?php namespace NozCore\Objects\Game;

use NozCore\ObjectBase;

class Drop extends ObjectBase {

    protected $table = 'npc_drop';

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {
        return [];
    }
}