<?php namespace NozCore\Objects\Site\DataTypes;

use NozCore\DataTypes;
use NozCore\ObjectBase;

class Article extends ObjectBase {

    protected $table = 'api_article';
    protected $defaultSort = 'created';
    protected $defaultSortOrder = 'desc';

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {

        return [
            'id'            => DataTypes::INTEGER,
            'header'        => DataTypes::STRING,
            'digest'        => DataTypes::STRING,
            'content'       => DataTypes::STRING,
            'created'       => DataTypes::TIMESTAMP,
            'edited'        => DataTypes::TIMESTAMP,
            'authorId'      => DataTypes::INTEGER,
            'pageId'        => DataTypes::INTEGER
        ];
    }
}