<?php

namespace brunoconte3\Validation;

class Format
{
    private const DATA_TYPE_TO_CONVERT = [
        'bool',
        'float',
        'int',
        'numeric'
    ];

    private static function returnTypeToConvert(array $rules): ?string
    {
        foreach (self::DATA_TYPE_TO_CONVERT as $type) {
            if (in_array($type, $rules)) {
                return $type;
            }
        }
        return null;
    }

    private static function executeConvert(string $type, $value)
    {
        switch ($type) {
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? (bool) $value : $value;
            case 'int':
                return filter_var($value, FILTER_VALIDATE_INT) ? (int) $value : $value;
            case 'float':
            case 'numeric':
                return filter_var($value, FILTER_VALIDATE_FLOAT) ? (float) $value : $value;
            default:
                return $value;
        }
    }

    public static function convertTypes(array &$data, array $rules)
    {
        $error = [];
        foreach ($rules as $key => $value) {
            $arrRules = explode('|', $value);
            $type = self::returnTypeToConvert($arrRules);
            if (in_array('convert', $arrRules) && !empty($type)) {
                try {
                    $data[$key] = self::executeConvert($type, $data[$key]);
                } catch (\Exception $ex) {
                    $error[] = "falhar ao tentar converter {$data[$key]} para $type";
                }
            }
        }
        if (!empty($error)) {
            return $error;
        }
    }

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

    public static function currency($valor) //Entrada Float ou String
    {
        return ($valor !== '') ? number_format($valor, 2, ',', '.') : '';
    }

    public static function ucwordsCharset(string $string, string $charset = 'UTF-8'): string
    {
        return mb_convert_case(mb_strtolower($string, $charset), MB_CASE_TITLE, 'UTF-8');
    }

    public static function pointOnlyValue(string $str): string
    {
        return preg_replace('/[^0-9]/', '.', preg_replace('/[^0-9,]/', '', $str));
    }

    public static function emptyToNull(array $array): array
    {
        return array_map(function ($value) {
            return (isset($value) && empty(trim($value)) || $value === 'null') ? null : $value;
        }, $array);
    }

    public static function mask($mask, $str): string
    {
        $str = str_replace(' ', '', $str);
        for ($i = 0; $i < strlen($str); $i++) {
            $mask[strpos($mask, "#")] = $str[$i];
        }
        return $mask;
    }

    public static function onlyNumbers(string $str): int
    {
        return preg_replace('/[^0-9]/', '', $str);
    }
}
