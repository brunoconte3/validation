<?php

namespace brunoconte3\Validation;

class ValidatePhone
{
    public static function validate(int $phone)
    {
        $array = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $phone = preg_replace('/\D+/', '', trim($phone));
        $numberDigits = strlen($phone);

        if ($numberDigits < 10 || $numberDigits > 11) {
            return false;
        }
        if (!self::substrCountArray($phone, $array)) {
            return false;
        }
        if (preg_match('/^[1-9]{2}([0-9]{8}|9[0-9]{8})$/', $phone)) {
            return true;
        }
        return false;
    }

    private static function substrCountArray(int $phone, array $array)
    {
        $count = 0;
        foreach ($array as $substring) {
            $count = substr_count($phone, $substring);
            if ($count >= 10) {
                return false;
            }
        }
        return true;
    }
}
