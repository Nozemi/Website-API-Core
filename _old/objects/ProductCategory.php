<?php namespace NozCore\Objects;

use ClanCats\Hydrahon\Query\Sql\Table;
use NozCore\DataTypes;
use NozCore\Message\Info;
use NozCore\ObjectBase;

/**
 * Class ProductCategory
 *
 * @property int id
 * @property array children
 *
 * @package NozCore\Objects
 */
class ProductCategory extends ObjectBase {

    protected $table = 'api_product_categories';

    protected static $fetchedChildren = false;

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {
        return [
            'id' => DataTypes::INTEGER,
            'order' => DataTypes::INTEGER,
            'name' => DataTypes::STRING,
            'thumbnail' => DataTypes::STRING,
            'description' => DataTypes::STRING,
            'parentId' => DataTypes::INTEGER,
            'parent' => DataTypes::OBJECT,
            'children' => DataTypes::OBJECT
        ];
    }

    protected $hooks = [
        'SUCCESSFUL_GET_EVENT' => [
            'getChildren'
        ]
    ];

    public function getAll($limit = false, $offset = false) {
        $products = [];

        /** @var Table $table */
        $query = $this->dbTable->select()
            ->whereNull('parentId')
            ->execute();

        foreach($query as $row) {
            $object = new $this($row);
            $this->callHooks('SUCCESSFUL_GET_EVENT', $object);
            $products[] = $object;
        }

        return $products;
    }

    public function getChildren(ProductCategory $object) {
        if(self::$fetchedChildren) {
            return;
        }

        $categories = $this->getChildCategories($object);
        $products = $this->getProducts($object);

        $object->children = array_merge($categories, $products);
        self::$fetchedChildren = true;
    }

    public function getChildCategories($object) {
        $result = $this->dbTable->select()
            ->orderBy('order', 'ASC')
            ->orderBy('id', 'DESC')
            ->where('parentId', $object->id)
            ->execute();

        $categories = [];
        foreach($result as $category) {
            $categories[] = new ProductCategory($category);
        }

        return $categories;
    }

    public function getProducts($object) {
        $product = new Product();
        return $product->getProductsByCategoryId($object->id);
    }
}