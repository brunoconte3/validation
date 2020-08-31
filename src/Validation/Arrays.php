<?php

namespace brunoconte3\Validation;

class Arrays
{
    private static function toKey($value)
    {
        return key([$value => null]);
    }

    public static function searchKey(array $array, $key): ?int
    {
        return Format::falseToNull(array_search(self::toKey($key), array_keys($array), true));
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
}
