<?php namespace NozCore\Objects\File;

use NozCore\DataTypes;

class Image extends File {

    public function data() {
         return array_merge(
             parent::data(),
             [
                 "width" => DataTypes::INTEGER,
                 "height" => DataTypes::INTEGER
             ]
         );
    }
}