<?php namespace NozCore\Objects;

use NozCore\DataTypes;
use NozCore\ObjectBase;

/**
 * Class Product
 *
 * @property int id
 * @property String name
 * @property String description
 * @property int priceId
 * @property int categoryId
 * @property ProductPrice price
 *
 * @package NozCore\Objects
 *
 */
class Product extends ObjectBase {

    protected $table = 'api_products';

    public function data() {

        return [
            'id'   => DataTypes::INTEGER,
            'name' => DataTypes::STRING,
            'description' => DataTypes::STRING,
            'categoryId' => DataTypes::INTEGER,
            'thumbnail' => DataTypes::STRING,
            'gallery' => DataTypes::JSON
        ];
    }

    /**
     * @return ObjectBase
     */
    public function getPrices() {
        if(!isset($this->id) || $this->id <= 0) {
            return null;
        }

        $price = new ProductPrice();
        return $price->getProductPrices($this->id);
    }

    public function getCategory() {
        if(!isset($this->categoryId) || $this->categoryId <= 0) {
            return null;
        }

        $category = new ProductCategory();
        return $category->get($this->categoryId);
    }

    public function getProductsByCategoryId($categoryId, $limit = 0, $offset = 0) {
        $result = $this->dbTable->select()
            ->orderBy('name', 'ASC')
            ->where('categoryId', $categoryId)
            ->execute();

        $products = [];
        foreach($result as $product) {
            $products[] = new Product($product);
        }

        return $products;
    }

    public function jsonSerialize() {
        $dataToSerialize = parent::jsonSerialize();
        $dataToSerialize['prices'] = $this->getPrices();
        $dataToSerialize['category'] = $this->getCategory();

        return $dataToSerialize;
    }
}