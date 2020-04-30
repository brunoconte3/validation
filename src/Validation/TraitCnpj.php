<?php

namespace brunoconte3\Validation;

trait TraitCnpj
{
    private function validateCnpjSequenceInvalidate(string $cnpj): bool
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

    private function validateRuleCnpj(string $cnpj): bool
    {
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

    private function dealCnpj(string $cnpj): string
    {
        $newCnpj = preg_match('/[0-9]/', $cnpj) ?
            str_replace(['-', '.', '/'], '', str_pad($cnpj, 14, '0', STR_PAD_LEFT), $cnpj) : 0;
        return $newCnpj;
    }

    public function formatCnpj(string $cnpj): string
    {
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '\$1.\$2.\$3/\$4-\$5', $cnpj);
    }

    public function validateCnpj(string $cnpj, bool $mask = true): bool
    {
        if (empty($cnpj)) {
            return false;
        }
        if ($mask) {
            $cnpj = $this->dealCnpj($cnpj);
        }

        if (!self::validateCnpjSequenceInvalidate($cnpj)) {
            return false;
        }
        return self::validateRuleCnpj($cnpj);
    }
}
