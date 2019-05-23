<?php namespace NozCore\Objects\Menu;

use NozCore\DataTypes;
use NozCore\ObjectBase;

class MenuItem extends ObjectBase {

    protected $table = 'api_menu_items';

    public function data() {

        return [
            'id' => DataTypes::INTEGER,
            'name' => DataTypes::STRING,
            'url' => DataTypes::STRING,
            'category' => DataTypes::INTEGER,
            'product' => DataTypes::INTEGER,
            'order' => DataTypes::INTEGER,
            'target' => DataTypes::STRING,
            'menuId' => DataTypes::INTEGER
        ];
    }
}