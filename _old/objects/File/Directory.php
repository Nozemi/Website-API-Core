<?php namespace NozCore\Objects\File;

use NozCore\DataTypes;

class Directory extends File {

    public function data() {

        return [
            'id' => DataTypes::STRING,
            'name' => DataTypes::STRING,
            'size' => DataTypes::DOUBLE,
            'created' => DataTypes::TIMESTAMP,
            'modified' => DataTypes::TIMESTAMP,
            'parent' => DataTypes::STRING
        ];
    }
}