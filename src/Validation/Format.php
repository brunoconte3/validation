<?php

namespace brunoconte3\Validation;

class Format
{
    public static function companyIdentification(string $cnpj): string
    {
        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj);
    }

    public static function identifier(string $cpf): string
    {
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cpf);
    }

    public static function telephone(int $number): string
    {
        $number = '(' . substr($number, 0, 2) . ') ' . substr($number, 2, -4) . '-' . substr($number, -4);
        return $number;
    }

    public static function zipCode(int $value): string
    {
        return substr($value, 0, 5) . '-' . substr($value, 5, 3);
    }

    public static function dateBrazil(string $date)
    {
        return date('d/m/Y', strtotime($date));
    }

    public static function dateAmerican(string $date)
    {
        if (strpos($date, '/') > -1) {
            return implode('-', array_reverse(explode('/', $date)));
        }
        return date('Y-m-d', strtotime($date));
    }

    public static function arrayToIntReference(array &$array): void
    {
        $array = array_map('intval', $array);
    }
}
