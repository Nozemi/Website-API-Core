<?php namespace NozCore\Objects\Game;

use NozCore\DataTypes;
use NozCore\ObjectBase;

class Drop extends ObjectBase {

    protected $table = 'game_npc_drop';

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {
        return [
            'id'         => DataTypes::INTEGER,
            'npcId'      => DataTypes::INTEGER,
            'itemId'     => DataTypes::INTEGER,
            'category'   => DataTypes::STRING,
            'minAmount'  => DataTypes::INTEGER,
            'maxAmount'  => DataTypes::INTEGER,
            'dropChance' => DataTypes::INTEGER,
        ];
    }

    public function get($id) {
        $objects = parent::get($id);
        return $this->parseDropsResult($objects);
    }

    /**
     * @return array
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function getAll() {
        $objects = parent::getAll();
        return $this->parseDropsResult($objects);
    }

    /**
     * @param array $filters
     *
     * @return array
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    public function getByFilters(array $filters) {
        $objects = parent::getByFilters($filters);
        return $this->parseDropsResult($objects);
    }

    /**
     * @param $objects
     *
     * @return array
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     * @throws \ReflectionException
     */
    private function parseDropsResult($objects) {

        $npcs = [];
        $items = [];
        $dropItems = [];
        foreach ($objects as $drop) {
            /** @var Drop $drop */

            if (!array_key_exists($drop->getProperty('npcId'), $npcs)) {
                $npc = new NPC();
                $npc = $npc->get($drop->getProperty('npcId'));
                $npcs[$npc->getProperty('id')] = $npc;
            }

            if (!array_key_exists($drop->getProperty('itemId'), $items)) {
                $item = new Item();
                $item = $item->get($drop->getProperty('itemId'));
                $items[$item->getProperty('itemId')] = $item;
            }

            $dropItems[$drop->getProperty('npcId')][] = [
                'id'         => $drop->getProperty('id'),
                'item'       => $items[$drop->getProperty('itemid')],
                'minAmount'  => $drop->getProperty('minAmount'),
                'maxAmount'  => $drop->getProperty('maxAmount'),
                'dropChance' => $drop->getProperty('dropChance'),
                'category'   => strtolower($drop->getProperty('category')),
            ];
        }

        $drops = [];
        foreach ($npcs as $npc) {
            /** @var NPC $npc */
            $drops[$npc->getProperty('id')] = [
                'id'    => $npc->getProperty('id'),
                'name'  => $npc->getProperty('name'),
                'drops' => $dropItems[$npc->getProperty('id')],
            ];
        }

        $objects = [];
        foreach ($drops as $drop) {
            $objects[] = $drop;
        }

        return $objects;
    }
}