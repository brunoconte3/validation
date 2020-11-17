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
     * @param float|int|string $value
     */
    private static function formatCurrencyForFloat($value): float
    {
        if (is_string($value)) {
            if (preg_match('/(\,|\.)/', substr(substr($value, -3), 0, 1))) {
                $value = (strlen(self::onlyNumbers($value)) > 0) ? self::onlyNumbers($value) : '000';
                $value = substr_replace($value, '.', -2, 0);
            } else {
                $value = (strlen(self::onlyNumbers($value)) > 0) ? self::onlyNumbers($value) : '000';
            };
        }
        return (float) $value;
    }

    /**
     * @param string|int $value
     */
    private static function validateForFormatting(string $nome, int $tamanho, $value): void
    {
        if (strlen($value) !== $tamanho) {
            throw new \Exception("$nome precisa ter $tamanho números!");
        }
        if (!is_numeric($value)) {
            throw new \Exception($nome . ' precisa conter apenas números!');
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
        self::validateForFormatting('companyIdentification', 14, $cnpj);
        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj);
    }

    public static function identifier(string $cpf): string
    {
        self::validateForFormatting('identifier', 11, $cpf);
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cpf);
    }

    public static function identifierOrCompany(string $cpfCnpj): string
    {
        if (strlen($cpfCnpj) === 11) {
            return self::identifier($cpfCnpj);
        } elseif (strlen($cpfCnpj) === 14) {
            return self::companyIdentification($cpfCnpj);
        } else {
            throw new \Exception('identifierOrCompany => Valor precisa ser um CPF ou CNPJ!');
        }
    }

    /**
     * @param string|int $number Pode receber uma String ou Inteiro, compatibilidade com sistemas que já usam
     */
    public static function telephone($number): string
    {
        if (strlen($number) < 10 || strlen($number) > 11) {
            throw new \Exception('telephone precisa ter 10 ou 11 números!');
        }
        if (!is_numeric($number)) {
            throw new \Exception('telephone precisa conter apenas números!');
        }
        $number = '(' . substr($number, 0, 2) . ') ' . substr($number, 2, -4) . '-' . substr($number, -4);
        return $number;
    }

    public static function zipCode(string $value): string
    {
        self::validateForFormatting('zipCode', 8, $value);
        return substr($value, 0, 5) . '-' . substr($value, 5, 3);
    }

    public static function dateBrazil(string $date)
    {
        if (strlen($date) < 8 || strlen($date) > 10) {
            throw new \Exception('dateBrazil precisa conter 8 à 10 dígitos!');
        }
        return date('d/m/Y', strtotime($date));
    }

    public static function dateAmerican(string $date)
    {
        if (strlen($date) < 8 || strlen($date) > 10) {
            throw new \Exception('dateAmerican precisa conter 8 à 10 dígitos!');
        }
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
     * @param float|int|string $value
     */
    public static function currency($value): string
    {
        if (!is_numeric($value)) {
            throw new \Exception('currency precisa ser do tipo numérico!');
        }

        $value = self::formatCurrencyForFloat($value);
        return ((float) $value !== '') ? number_format((float) $value, 2, ',', '.') : '';
    }

    /**
     * @param float|int|string $value
     */
    public static function currencyUsd($value): string
    {
        if (!is_numeric($value)) {
            throw new \Exception('currencyUsd precisa ser do tipo numérico!');
        }

        $value = self::formatCurrencyForFloat($value);
        return ((float) $value !== '') ?  number_format((float) $value, 2, '.', ',') : '';
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

    public static function removeAccent(?string $string): ?string
    {
        if (empty($string)) {
            return null;
        }
        return preg_replace(
            [
                '/(á|à|ã|â|ä)/',
                '/(Á|À|Ã|Â|Ä)/',
                '/(é|è|ê|ë)/',
                '/(É|È|Ê|Ë)/',
                '/(í|ì|î|ï)/',
                '/(Í|Ì|Î|Ï)/',
                '/(ó|ò|õ|ô|ö)/',
                '/(Ó|Ò|Õ|Ô|Ö)/',
                '/(ú|ù|û|ü)/',
                '/(Ú|Ù|Û|Ü)/',
                '/(ñ)/',
                '/(Ñ)/',
                '/(ç)/',
                '/(Ç)/',
            ],
            explode(' ', 'a A e E i I o O u U n N c C'),
            $string
        );
    }
}
