<?php namespace NozCore\Objects;

use NozCore\DataTypes;
use NozCore\ObjectBase;

/**
 * Class ProductPrice
 *
 * @property int id
 * @property int currencyId
 * @property int productId
 * @property double value
 *
 * @package NozCore\Objects
 */
class ProductPrice extends ObjectBase {

    protected $table = 'api_product_prices';

    /**
     * Define the table structure in an array with key being column name and value being data type.
     *
     * @return array
     */
    public function data() {

        return [
            'id' => DataTypes::INTEGER,
            'currencyId' => DataTypes::INTEGER,
            'productId' => DataTypes::INTEGER,
            'value' => DataTypes::DOUBLE,
            'vat' => DataTypes::INTEGER
        ];
    }

    /**
     * @return ObjectBase
     * @throws \ClanCats\Hydrahon\Query\Sql\Exception
     */
    public function getCurrency() {
        $currency = new Currency();
        return $currency->get($this->currencyId);
    }

    public function jsonSerialize() {
        $dataToSerialize = parent::jsonSerialize();
        $dataToSerialize['currency'] = $this->getCurrency();

        return $dataToSerialize;
    }

    public function getProductPrices($productId) {
        $result = $this->dbTable->select()
            ->where('productId', $productId)
            ->execute();

        if(!empty($result)) {
            $prices = [];
            foreach($result as $price) {
                $prices[] = new $this($price);
            }

            return $prices;
        }

        return null;
    }
}