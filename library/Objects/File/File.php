<?php namespace NozCore\Objects\File;

use NozCore\DataTypes;
use NozCore\ObjectBase;

class File extends ObjectBase {

    protected $table = 'cdn_file_map';

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {
        return [
            'id' => DataTypes::STRING,
            'name' => DataTypes::STRING,
            'size' => DataTypes::DOUBLE,
            'created' => DataTypes::TIMESTAMP,
            'modified' => DataTypes::TIMESTAMP,
            'location' => DataTypes::STRING,
            'original' => DataTypes::STRING,
            'extension' => DataTypes::STRING,
            'parent' => DataTypes::STRINGcdn_fi
        ];
    }

    /**
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function generateId() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for($i = 0; $i < 48; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $file = $this->get($randomString);

        if($file instanceof File) {
            $randomString = $this->generateId();
        }

        $this->setProperty('id', $randomString);
    }
}