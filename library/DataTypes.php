<?php namespace NozCore;

class DataTypes {
    const STRING = 0;
    const STR = self::STRING;
    const INTEGER = 2;
    const INT = self::INTEGER;
    const DOUBLE = 3;
    const FLOAT = 4;
    const BOOLEAN = 5;
    const BOOL = self::BOOLEAN;
    const OBJECT = 6;
    const TIMESTAMP = 7;
    const JSON = 8;

    public static function parseValue($value, $type) {
        switch($type) {
            case self::STRING:
                return self::parseString($value);
                break;
            case self::INTEGER:
                return self::parseInteger($value);
                break;
            case self::DOUBLE:
                return self::parseDouble($value);
                break;
            case self::FLOAT:
                return self::parseFloat($value);
                break;
            case self::BOOLEAN:
                return self::parseBoolean($value);
                break;
            case self::OBJECT:
                return self::parseObject($value);
                break;
            case self::TIMESTAMP:
                return self::parseTimeStamp($value);
                break;
            case self::JSON:
                return self::parseJson($value);
                break;
        }

        // Getting here means that parse failed.
        return false;
    }

    public static function parseString($value) {

        return $value;
    }

    public static function parseInteger($value) {
        $value = intval($value);

        return $value;
    }

    public static function parseBoolean($value) {
        $value = boolval($value);

        return $value;
    }

    public static function parseFloat($value) {

        return $value;
    }

    public static function parseDouble($value) {
        $value = doubleval($value);

        return $value;
    }

    public static function parseObject($value) {

        return $value;
    }

    public static function parseTimeStamp($value) {

        return $value;
    }

    public static function parseJson($value) {
        $jsonValue = json_decode($value);

        if(json_last_error() == JSON_ERROR_NONE) {
            return $jsonValue;
        }

        return null;
    }
}