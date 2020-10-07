<?php

declare(strict_types=1);

namespace brunoconte3\Validation;

use brunoconte3\Validation\Format;

class Arrays
{
    public static function searchKey(array $array, $key): ?int
    {
        return Format::falseToNull(array_search(key([$key => null]), array_keys($array), true));
    }

    public static function renameKey(array &$array, $oldKey, $newKey): bool
    {
        $offset = self::searchKey($array, $oldKey);
        if ($offset === null) {
            return false;
        }
        $val = &$array[$oldKey];
        $keys = array_keys($array);
        $keys[$offset] = $newKey;
        $array = array_combine($keys, $array);
        $array[$newKey] = &$val;
        return true;
    }

    public static function checkExistIndexByValue(array $arrayCollection, string $search): bool
    {
        foreach ($arrayCollection as $array) {
            $indice = (!is_array($array) && ((string) $search == (string) $array)) ? true : false;
            if ((is_array($array) && self::checkExistIndexByValue($array, $search)) || $indice) {
                unset($indice);
                return true;
            }
        }
        return false;
    }

    public static function findValueByKey(array $array, string $searchKey): array
    {
        $retorno = [];
        foreach ($array as $key => $value) {
            if ((string) strtolower($key) === strtolower($searchKey)) {
                $retorno[$key] = $value;
            } else {
                if (is_array($value)) {
                    $retorno[$key] = self::findValueByKey($value, $searchKey);
                }
            }
        }
        return array_filter($retorno);
    }

    /**
     * @param string|int|boolean $searchValue
     */
    public static function findIndexByValue(array $array, $searchValue): array
    {
        $retorno = [];
        foreach ($array as $key => $value) {
            if (!is_array($value) && ($value === $searchValue)) {
                $retorno[$key] = $value;
            } else {
                if (is_array($value)) {
                    $retorno[$key] = self::findIndexByValue($value, $searchValue);
                }
            }
        }
        return array_filter($retorno);
    }

    public static function convertArrayToXml(array $array, object &$xml): void
    {
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                $key = $value['@attr'];
            }
            if (is_array($value)) {
                unset($value['@attr']);
                $subnode = $xml->addChild($key);

                self::convertArrayToXml($value, $subnode);
            } else {
                $xml->addChild("$key", strtoupper(htmlspecialchars("$value")));
            }
        }
    }

    public static function convertJsonIndexToArray(array &$array): void
    {
        array_walk_recursive($array, function (&$value, $key) {
            if (is_string($value) && !empty($value)) {
                $arr = json_decode($value, true);
                if (is_array($arr) && (json_last_error() === JSON_ERROR_NONE)) {
                    $value = $arr;
                }
            }

            if (is_array($value)) {
                self::convertJsonIndexToArray($value);
            }
        });
    }
}
