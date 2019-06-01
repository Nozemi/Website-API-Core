<?php namespace NozCore\Objects\Menu;

use NozCore\DataTypes;
use NozCore\ObjectBase;

/**
 * Class Menu
 *
 * @property int id
 * @property string items
 *
 * @package NozCore\Objects\Menu
 */
class Menu extends ObjectBase {

    protected $table = 'api_menus';

    public function data() {

        return [
            'id' => DataTypes::INTEGER,
            'name' => DataTypes::STRING,
            'items' => DataTypes::OBJECT
        ];
    }

    protected $hooks = [
        'SUCCESSFUL_GET_EVENT' => [
            'getItems'
        ]
    ];

    public function getItems(Menu $object) {
        $menuItems = new MenuItem();

        $result = $menuItems->dbTable->select()
            ->orderBy('order', 'ASC')
            ->orderBy('id', 'DESC')
            ->where('menuId', $object->id)
            ->execute();

        $items = [];

        foreach($result as $menuItem) {
            $items[] = new MenuItem($menuItem);
        }

        $object->items = $items;
    }
}