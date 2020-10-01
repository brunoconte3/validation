<?php

namespace brunoconte3\Validation;

use brunoconte3\Validation\ValidatePhone;

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

    /**
     * @param float|int|string $valor
     */
    private static function formatCurrencyForFloat($valor): float
    {
        if (is_string($valor)) {
            if (preg_match('/(\,|\.)/', substr(substr($valor, -3), 0, 1))) {
                $valor = (strlen(self::onlyNumbers($valor)) > 0) ? self::onlyNumbers($valor) : '000';
                $valor = substr_replace($valor, '.', -2, 0);
            } else {
                $valor = (strlen(self::onlyNumbers($valor)) > 0) ? self::onlyNumbers($valor) : '000';
            };
        }
        return (float) $valor;
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

    public static function zipCode(string $value): string
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

    public static function arrayToInt(array $array): array
    {
        return array_map('intval', $array);
    }

    /**
     * @param float|int|string $valor
     */
    public static function currency($valor): string
    {
        $valor = self::formatCurrencyForFloat($valor);
        return ((float) $valor !== '') ? number_format((float) $valor, 2, ',', '.') : '';
    }

    /**
     * @param float|int|string $valor
     */
    public static function currencyUsd($valor): string
    {
        $valor = self::formatCurrencyForFloat($valor);
        return ((float) $valor !== '') ?  number_format((float) $valor, 2, '.', ',') : '';
    }

    /**
     * @return string|bool
     */
    public static function returnPhoneOrAreaCode(string $phone, bool $areaCode = false)
    {
        $phone = self::onlyNumbers($phone);
        if (!empty($phone) && ValidatePhone::validate($phone)) {
            return ($areaCode) ? preg_replace('/\A.{2}?\K[\d]+/', '', $phone) : preg_replace('/^\d{2}/', '', $phone);
        }
        return false;
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

    public static function onlyNumbers(string $str): string
    {
        return preg_replace('/[^0-9]/', '', $str);
    }

    public static function onlyLettersNumbers(string $str): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $str);
    }

    public static function upper(string $string, string $charset = 'UTF-8'): string
    {
        return mb_strtoupper($string, $charset);
    }

    public static function lower(string $string, string $charset = 'UTF-8'): string
    {
        return mb_strtolower($string, $charset);
    }

    public static function reverse(string $string, string $charSet = 'UTF-8'): string
    {
        if (!extension_loaded('iconv')) {
            throw new \Exception(__METHOD__ . '() requires ICONV extension that is not loaded.');
        }
        return iconv('UTF-32LE', $charSet, strrev(iconv($charSet, 'UTF-32BE', $string)));
    }

    public static function falseToNull($value)
    {
        return $value === false ? null : $value;
    }
}
