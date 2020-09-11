<?php

namespace brunoconte3\Validation;

class ValidatePhone
{
    public static function validate(string $phone): bool
    {
        $phone = (int) Format::onlyNumbers($phone);

        $phone = preg_replace('/\D+/', '', trim($phone));
        $numberDigits = strlen($phone);

        if ($numberDigits < 10 || $numberDigits > 11) {
            return false;
        }
        if (preg_match('/^[1-9]{2}([0-9]{8}|9[0-9]{8})$/', $phone)) {
            return true;
        }
        return false;
    }
}
