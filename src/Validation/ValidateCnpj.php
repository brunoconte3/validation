<?php

namespace brunoconte3\Validation;

class ValidateCnpj
{
    private static function validateCnpjSequenceInvalidate(string $cnpj): bool
    {
        $cnpjInvalidate = [
            '00000000000000', '11111111111111', '22222222222222', '33333333333333', '44444444444444',
            '55555555555555', '66666666666666', '77777777777777', '88888888888888', '99999999999999'
        ];
        if (in_array($cnpj, $cnpjInvalidate)) {
            return false;
        }
        return true;
    }

    private static function validateRuleCnpj(string $cnpj): bool
    {
        if (strlen($cnpj) > 14) {
            $cnpj = self::dealCnpj($cnpj);
        }

        for ($i = 0, $j = 5, $sum = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $rest = $sum % 11;
        if ($cnpj[12] != ($rest < 2 ? 0 : 11 - $rest)) {
            return false;
        }
        for ($i = 0, $j = 6, $sum = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $rest = $sum % 11;
        $res = $cnpj[13] == ($rest < 2 ? 0 : 11 - $rest);
        return $res;
    }

    private static function dealCnpj(string $cnpj): string
    {
        $newCnpj = preg_match('/[0-9]/', $cnpj) ?
            str_replace(['-', '.', '/'], '', str_pad($cnpj, 14, '0', STR_PAD_LEFT), $cnpj) : 0;
        return $newCnpj;
    }

    public static function validateCnpj(string $cnpj): bool
    {
        if (empty($cnpj)) {
            return false;
        }
        if (strlen($cnpj) > 14) {
            $cnpj = self::dealCnpj($cnpj);
        }

        if (!self::validateCnpjSequenceInvalidate($cnpj)) {
            return false;
        }
        return self::validateRuleCnpj($cnpj);
    }
}
