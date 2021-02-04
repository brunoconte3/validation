<?php

namespace brunoconte3\Validation;

use brunoconte3\Validation\{
    ValidateCpf,
    ValidateCnpj,
    ValidatePhone,
    ValidateHour,
    ValidateFile
};

class Rules
{
    protected $errors = false;
    public const RULES_WITHOUT_FUNCS = ['convert'];

    private function invalidRule($rule = '', $field = '', $value = null, $message = null)
    {
        $msg = "Uma regra inválida está sendo aplicada no campo $field!";
        $this->errors[$field] = $msg;
    }

    private function validateHandleErrorsInArray(array $errorList = [], string $field = ''): void
    {
        if (count($errorList) > 0) {
            if ((is_array($this->errors) && array_key_exists($field, $this->errors))) {
                foreach ($errorList as $error) {
                    array_push($this->errors[$field], $error);
                }
                $this->errors[$field] = array_unique($this->errors[$field]);
            } else {
                $this->errors[$field] = $errorList;
            }
        }
    }

    protected function prepareCharset(string $string = '', string $convert = 'UTF-8', bool $bom = false): string
    {
        $bomchar = pack('H*', 'EFBBBF');
        $string = trim(preg_replace("/^$bomchar/", '', $string));
        static $enclist = [
            'UTF-8', 'ASCII',
            'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
            'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
            'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
            'Windows-1251', 'Windows-1252', 'Windows-1254'
        ];
        $charsetType = mb_detect_encoding($string);
        foreach ($enclist as $item) {
            $converted = iconv($item, $item . '//IGNORE', $string);
            if (md5($converted) == md5($string)) {
                $charsetType = $item;
                break;
            }
        }
        if (strtoupper(trim($charsetType)) != strtoupper(trim($convert))) {
            return ($bom ? $bomchar : '') . iconv($charsetType, $convert . '//TRANSLIT', $string);
        }
        return ($bom ? $bomchar : '') . $string;
    }

    public static function functionsValidation(): array
    {
        return [
            'optional' => 'validateOptional',
            'required' => 'validateFieldMandatory',
            'type' => 'validateFieldType',
            'min' => 'validateMinimumField',
            'max' => 'validateMaximumField',
            'alpha' => 'validateAlphabets',
            'alphaNoSpecial' => 'validateAlphaNoSpecial',
            'alphaNum' => 'validateAlphaNumerics',
            'alphaNumNoSpecial' => 'validateAlphaNumNoSpecial',
            'array' => 'validateArray',
            'arrayValues' => 'validateArrayValues',
            'bool' => 'validateBoolean',
            'companyIdentification' => 'validateCompanyIdentification',
            'dateBrazil' => 'validateDateBrazil',
            'dateAmerican' => 'validateDateAmerican',
            'email' => 'validateEmail',
            'float' => 'validateFloating',
            'hour' => 'validateHour',
            'identifier' => 'validateIdentifier',
            'identifierOrCompany' => 'validateIdentifierOrCompany',
            'int' => 'validateInteger',
            'ip' => 'validateIp',
            'lower' => 'validateLower',
            'mac' => 'validateMac',
            'notSpace' => 'validateSpace',
            'numeric' => 'validateNumeric',
            'numMax' => 'validateNumMax',
            'numMonth' => 'validateNumMonth',
            'numMin' => 'validateNumMin',
            'phone' => 'validatePhone',
            'plate' => 'validatePlate',
            'regex' => 'validateRegex',
            'upper' => 'validateUpper',
            'url' => 'validateUrl',
            'noWeekend' => 'validateWeekend',
            'zipcode' => 'validateZipCode',
            'json' => 'validateJson',
            'maxUploadSize' => 'validateFileMaxUploadSize',
            'minUploadSize' => 'validateFileMinUploadSize',
            'fileName' => 'validateFileName',
            'mimeType' => 'validateFileMimeType',
            'requiredFile' => 'validateFileUploadMandatory',
            'maxFile' => 'validateMaximumFileNumbers',
            'minFile' => 'validateMinimumFileNumbers'
        ];
    }

    protected function validateOptional(): bool
    {
        return true;
    }

    protected function validateFieldMandatory($rule = '', $field = '', $value = null, $message = null)
    {
        if (is_array($value)) {
            if (count($value) <= 0) {
                $this->errors[$field] = !empty($message) ? $message : "O campo $field é obrigatório!";
            }
        } else {
            if (empty(trim($value))) {
                $this->errors[$field] = !empty($message) ? $message : "O campo $field é obrigatório!";
            }
        }
    }

    protected function validateFieldType($rule = '', $field = '', $value = null, $message = null)
    {
        if (in_array(trim(strtolower($rule)), self::RULES_WITHOUT_FUNCS)) {
            return;
        }

        $method = trim(self::functionsValidation()[trim($rule)] ?? 'invalidRule');

        $call = [$this, $method];
        //chama há função de validação, de cada parametro json
        if (is_callable($call, true, $method)) {
            call_user_func_array($call, [$rule, $field, $value, $message]);
        } else {
            $this->errors[$field] = "Há regras de validação não implementadas no campo $field!";
        }
    }

    protected function levelSubLevelsArrayReturnJson(array $data, bool $recursive = false)
    {
        //funcao recurssiva para tratar array e retornar json valido
        //essa função serve para validar dados com json_encode múltiplos, e indices quebrados na estrutura
        foreach ($data as $key => $val) {
            $key = $this->prepareCharset($key, 'UTF-8');
            if (is_string($val) && !empty($val)) {
                $arr = json_decode($val, true);
                if (is_array($arr) && (json_last_error() === JSON_ERROR_NONE)) {
                    $val = $arr;
                }
            }
            if (is_array($val)) {
                $data[$key] = $this->levelSubLevelsArrayReturnJson($val, true);
            } elseif (is_string($val)) {
                $data[$key] = $this->prepareCharset(addslashes($val), 'UTF-8');
            }
        }
        if ($recursive) {
            return $data;
        }
        //se for raiz retorna json
        return strtr(stripslashes(json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        )), ["\r" => '', "\n" => '', "\t" => '']);
    }

    protected function validateSubLevelData(
        array $data,
        array $rules
    ) {
        //percorre o array de validação para não rodar recurssivamente atoa
        foreach ($rules as $key => $val) {
            //se for um objeto no primeiro nivel, valida recurssivo
            if ((array_key_exists($key, $data) && is_array($data[$key])) && is_array($val)) {
                $this->validateSubLevelData($data[$key], $rules[$key]);
            }
            //valida campos filhos required, porém não existe no array de dados
            if (
                empty($data) && is_array($val) &&
                (strpos(trim(strtolower(json_encode($val))), 'required') !== false)
            ) {
                $this->errors[$key] = "Não foi encontrado o indice $key, campos filhos são obrigatórios!";
                return false;
            }
            //validação campo a campo
            if (is_string($val)) {
                $this->validateRuleField($key, ($data[$key] ?? null), $val, array_key_exists($key, $data));
            }
        }
        return $rules;
    }

    protected function validateRuleField($field, $value, $rules, $valid = false)
    {
        //se o campo é valido, ele existe no json de dados, no mesmo nivel que a regra
        if ($valid) {
            //transforma a string json de validação em array para validação
            $rulesArray = is_array($rules) ? $rules : [];
            if (is_string($rules) && !empty($rules)) {
                $rulesArray = json_decode($rules, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $rulesArray = [];
                    //suporte ao padrão PIPE
                    //'int|required|min:14|max:14',
                    $rulesConf = explode('|', trim($rules));
                    if (
                        !in_array('optional', $rulesConf)
                        || (in_array('optional', $rulesConf) && !empty($value) && $value !== 'null')
                    ) {
                        foreach ($rulesConf as $valueRuleConf) {
                            $conf = explode(',', trim($valueRuleConf));
                            $ruleArrayConf = explode(':', $conf[0] ?? '');
                            $regEx = (trim(strtolower($ruleArrayConf[0])) == 'regex') ? true : false;

                            if (isset($ruleArrayConf[1]) && (strpos($valueRuleConf, ';') > 0) && !$regEx) {
                                $ruleArrayConf[1] = explode(';', $ruleArrayConf[1]);
                            }

                            if (array_key_exists(1, $conf) && !empty($conf[1])) {
                                $rulesArray['mensagem'] = trim(strip_tags($conf[1]));
                            }

                            if (!empty($ruleArrayConf)) {
                                $rulesArray[$ruleArrayConf[0] ?? (count($rulesArray) + 1)] = $ruleArrayConf[1] ?? true;
                            }
                        }
                        //--------------------------------------------------
                        if (empty($rulesArray)) {
                            $this->errors[$field] = "Há errors no json de regras de validação do campo $field!";
                        }
                    }
                }
            }
            $rulesArray = !empty($rulesArray) && is_array($rulesArray) ? $rulesArray : [];
            //irá chamar uma função para cada validação no json de validação, passando o valor para a função
            $msgCustomized = $rulesArray['mensagem'] ?? null;
            if (array_key_exists('mensagem', $rulesArray)) {
                unset($rulesArray['mensagem']);
            }
            foreach ($rulesArray as $key => $val) {
                if (!in_array('optional', $rulesArray) || (in_array('optional', $rulesArray) && !empty($val))) {
                    if (in_array(trim(strtolower($key)), self::RULES_WITHOUT_FUNCS)) {
                        continue;
                    }
                    $method = trim(Rules::functionsValidation()[trim($key)] ?? 'invalidRule');
                    $call = [$this, $method];
                    //chama a função de validação, de cada parametro json
                    if (is_callable($call, true, $method)) {
                        call_user_func_array($call, [$val, $field, $value, $msgCustomized]);
                    } else {
                        $this->errors[$field] = "Há regras de validação não implementadas no campo $field!";
                    }
                }
            }
        } else {
            //se o campo é invalido, ele não existe no json de dados no mesmo nivel que a regra
            //aqui valida se na regra há filhos obrigatorios para esse campo
            $rulesArray = is_array($rules) ? $rules : [];
            if (is_string($rules) && !empty($rules)) {
                $rulesArray = json_decode($rules, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $rulesArray = [];
                    //suporte ao padrão PIPE
                    //'int|required|min:14|max:14',
                    $rulesConf = explode('|', trim($rules));
                    foreach ($rulesConf as $valueRuleConf) {
                        $ruleArrayConf =  explode(':', trim($valueRuleConf));
                        if (!empty($ruleArrayConf)) {
                            $rulesArray[$ruleArrayConf[0] ?? (count($rulesArray) + 1)] = $ruleArrayConf[1] ?? true;
                        }
                    }
                    if (empty($rulesArray)) {
                        $this->errors[$field] = "Há errors no json de regras de validação do campo $field!";
                    }
                    //$this->errors[$field] = "Há regras de validação não implementadas no campo $field!";
                }
            }
            $rulesArray = is_array($rulesArray) ? $rulesArray : [];
            $jsonRules = $this->levelSubLevelsArrayReturnJson($rulesArray);
            $compareA = strpos(trim(strtolower($jsonRules)), 'required');
            if ($compareA !== false) {
                $msg = "O campo $field não foi encontrado nos dados de entrada, indices filhos são obrigatórios!";
                if (count(array_filter(array_values(json_decode($jsonRules, true)), 'is_array')) == 0) {
                    $msg = "O campo obrigátorio $field não foi encontrado nos dados de entrada!";
                }
                $this->errors[$field] = $msg;
            }
            return $this->errors;
        }
    }

    protected function validateMinimumField($rule = '', $field = '', $value = null, $message = null)
    {
        if (strlen($value) < $rule) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field precisa conter no mínimo $rule caracteres!";
        }
    }

    protected function validateMaximumField($rule = '', $field = '', $value = null, $message = null)
    {
        if (strlen($value) > $rule) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field precisa conter no máximo $rule caracteres!";
        };
    }

    protected function validateAlphabets($rule = '', $field = '', $value = null, $message = null)
    {
        if (
            !preg_match(
                '/^([a-zA-ZÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/',
                $value
            ) !== false
        ) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter caracteres alfabéticos!";
        }
    }

    protected function validateAlphaNoSpecial($rule = '', $field = '', $value = null, $message = null)
    {
        if (!preg_match('/^([a-zA-Z\s])+$/', $value) !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter caracteres alfabéticos regular, não pode ter ascentos!";
        }
    }

    protected function validateAlphaNumNoSpecial($rule = '', $field = '', $value = null, $message = null)
    {
        if (!preg_match('/^([a-zA-Z0-9\s])+$/', $value) !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter letras sem acentos e números, não pode carácter especial!";
        }
    }

    protected function validateAlphaNumerics($rule = '', $field = '', $value = null, $message = null)
    {
        if (
            !preg_match(
                '/^([a-zA-Z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/',
                $value
            ) !== false
        ) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter caracteres alfanuméricos!";
        }
    }

    protected function validateArray($rule = '', $field = '', $value = null, $message = null)
    {
        if (!is_array($value)) {
            $this->errors[$field] = !empty($message) ? $message : "A variável $field não é um array!";
        }
    }

    protected function validateArrayValues($rule = '', $field = '', $value = null, $message = null)
    {
        $ruleArray = explode('-', $rule);

        if (!in_array(trim($value), $ruleArray)) {
            $this->errors[$field] = !empty($message)
                ? $message
                : "O campo $field precisa conter uma das opções [" . str_replace('-', ',', $rule) . '] !';
        }
    }

    protected function validateBoolean($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter valores lógicos. (true, 1, yes)!";
        }
    }

    protected function validateCompanyIdentification($rule = '', $field = '', $value = null, $message = null)
    {
        if (is_numeric($value) && strlen($value) === 14) {
            $value = Format::mask('##.###.###/####-##', $value);
        }
        if (empty($value) || !ValidateCnpj::validateCnpj($value, $rule)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field é inválido!";
        }
    }

    protected function validateDateBrazil($rule = '', $field = '', $value = null, $message = null)
    {
        if (is_numeric($value) && strlen($value) === 8) {
            $value = Format::mask('##/##/####', $value);
        }
        if (empty($value) || !ValidateDate::validateDateBrazil($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é uma data válida!";
        }
    }

    protected function validateDateAmerican($rule = '', $field = '', $value = null, $message = null)
    {
        if (is_numeric($value) && strlen($value) === 8) {
            $value = Format::mask('####-##-##', $value);
        }
        if (empty($value) || !ValidateDate::validateDateAmerican($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é uma data válida!";
        }
    }

    protected function validateEmail($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser um endereço de e-mail válido!";
        }
    }

    protected function validateFloating($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser do tipo real(flutuante)!";
        }
    }

    protected function validateHour($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ValidateHour::validateHour($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é uma hora válida!";
        }
    }

    protected function validateIdentifier($rule = '', $field = '', $value = null, $message = null)
    {
        if (strlen($value) === 11) {
            $value = Format::mask('###.###.###-##', $value);
        }
        if (!ValidateCpf::validateCpf($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field é inválido!";
        }
    }

    protected function validateIdentifierOrCompany($rule = '', $field = '', $value = null, $message = null)
    {
        if (strlen($value) === 11) {
            $value = Format::mask('###.###.###-##', $value);
        }
        if (is_numeric($value) && strlen($value) === 14) {
            $value = Format::mask('##.###.###/####-##', $value);
        }
        if (strlen($value) === 14) {
            if (!ValidateCpf::validateCpf($value, $rule)) {
                $this->errors[$field] = !empty($message) ?
                    $message : "O campo $field é inválido!";
            }
        }
        if (strlen($value) === 18) {
            if (empty($value) || !ValidateCnpj::validateCnpj($value, $rule)) {
                $this->errors[$field] = !empty($message) ?
                    $message : "O campo $field é inválido!";
            }
        }
    }

    protected function validateInteger($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser do tipo inteiro!";
        }
    }

    protected function validateIp($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_IP)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser um endereço de IP válido!";
        }
    }

    protected function validateLower($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ctype_lower(preg_replace('/\W+/', '', $value))) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field precisa ser tudo minúsculo!";
        }
    }

    protected function validateMac($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_MAC)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser um endereço de MAC válido!";
        }
    }

    protected function validateSpace($rule = '', $field = '', $value = null, $message = null)
    {
        if (strpos($value, ' ') !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não pode conter espaço!";
        }
    }

    protected function validateNumeric($rule = '', $field = '', $value = null, $message = null)
    {
        if (!is_numeric($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter valores numéricos!";
        }
    }

    protected function validateNumMax($rule = '', $field = '', $value = null, $message = null)
    {
        if ($value > $rule) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field é permitido até o valor máximo de $rule!";
        }
    }

    protected function validateNumMonth($rule = '', $field = '', $value = null, $message = null)
    {
        if (!is_int((int) $value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field precisa ser do valor inteiro!";
        }
        if ($value > 12 || $value <= 0 || strlen((string)$value) > 2) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é um mês válido!";
        }
    }

    protected function validateNumMin($rule = '', $field = '', $value = null, $message = null)
    {
        if (!is_int((int) $value)) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não é um inteiro!";
        }
        if ($value < $rule) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ter o valor mínimo de $rule!";
        }
    }

    protected function validatePlate($rule = '', $field = '', $value = null, $message = null)
    {
        if (!preg_match('/^[A-Z]{3}-[0-9]{4}+$/', $value) !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve corresponder ao formato AAA-0000!";
        }
    }

    protected function validateRegex($rule = '', $field = '', $value = null, $message = null)
    {
        if (!preg_match($rule, $value) !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field precisa conter um valor com formato válido!";
        }
    }

    protected function validateUrl($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser um endereço de URL válida!";
        }
    }

    protected function validatePhone($rule = '', $field = '', $value = null, $message = null)
    {
        if (is_numeric($value) && in_array(strlen($value), [10, 11])) {
            if (strlen($value) === 10) {
                $value = Format::mask('(##)####-####', $value);
            }
            if (strlen($value) === 11) {
                $value = Format::mask('(##)#####-####', $value);
            }
        }
        if (!ValidatePhone::validate($value)) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não é um telefone válido!";
        }
    }

    protected function validateUpper($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ctype_upper(preg_replace('/\W+/', '', $value))) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field precisa ser tudo maiúsculo!";
        }
    }

    protected function validateWeekend($rule = '', $field = '', $value = null, $message = null)
    {
        if (strpos($value, '/') > -1) {
            $value = Format::dateAmerican($value);
        }
        $day = date('w', strtotime($value));
        if (in_array($day, [0, 6])) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não pode ser um Final de Semana!";
        }
    }

    protected function validateZipCode($rule = '', $field = '', $value = null, $message = null)
    {
        if (is_numeric($value) && strlen($value) === 8) {
            $value = Format::mask('#####-###', $value);
        }
        if (!preg_match('/^([0-9]{2}[0-9]{3}-[0-9]{3})+$/', $value) !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve corresponder ao formato 00000-000!";
        }
    }

    protected function validateJson($rule = '', $field = '', $value = null, $message = null)
    {
        $value = is_array($value) ? json_encode($value) : $value;
        if (is_string($value)) {
            $arr = json_decode($value, true);
            if (!is_array($arr) || (json_last_error() !== JSON_ERROR_NONE)) {
                $this->errors[$field] = !empty($message) ? $message : "O campo $field não contém um json válido!";
            }
        } else {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não contém um json válido!";
        }
    }

    protected function validateFileMaxUploadSize($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);

        if (!is_numeric($rule) || ($rule < 0)) {
            $text = "O parâmetro do validador 'maxUploadSize', deve ser numérico e maior ou igual a zero!";
            $text = !empty($message) ? $message : $text;
            $this->errors[$field] = $text;
            return;
        }

        $validateErrorPhp = ValidateFile::validateFileErrorPhp($value, $message);

        if (empty($validateErrorPhp)) {
            $validateResult = ValidateFile::validateMaxUploadSize(intval($rule), $value, $message);
            $this->validateHandleErrorsInArray($validateResult, $field);
        } else {
            $this->validateHandleErrorsInArray($validateErrorPhp, $field);
        }
    }

    protected function validateFileMinUploadSize($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);

        if (!is_numeric($rule) || ($rule < 0)) {
            $text = "O parâmetro do validador 'minUploadSize', deve ser numérico e maior ou igual a zero!";
            $text = !empty($message) ? $message : $text;
            $this->errors[$field] = $text;
            return;
        }

        $validateErrorPhp = ValidateFile::validateFileErrorPhp($value, $message);

        if (empty($validateErrorPhp)) {
            $validateResult = ValidateFile::validateMinUploadSize(intval($rule), $value, $message);
            $this->validateHandleErrorsInArray($validateResult, $field);
        } else {
            $this->validateHandleErrorsInArray($validateErrorPhp, $field);
        }
    }

    protected function validateFileName($rule = '', $field = '', $value = null, $message = null): void
    {
        if (empty($value) || (count($value) <= 0)) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não pode ser vazio!";
            return;
        }

        $validateErrorPhp = ValidateFile::validateFileErrorPhp($value, $message);

        if (empty($validateErrorPhp)) {
            $validateResult = ValidateFile::validateFileName($value, $message);
            $this->validateHandleErrorsInArray($validateResult, $field);
        } else {
            $this->validateHandleErrorsInArray($validateErrorPhp, $field);
        }
    }

    protected function validateFileMimeType($rule = '', $field = '', $value = null, $message = null): void
    {
        $validateErrorPhp = ValidateFile::validateFileErrorPhp($value, $message);

        if (empty($validateErrorPhp)) {
            $validateResult = ValidateFile::validateMimeType($rule, $value, $message);
            $this->validateHandleErrorsInArray($validateResult, $field);
        } else {
            $this->validateHandleErrorsInArray($validateErrorPhp, $field);
        }
    }

    protected function validateFileUploadMandatory($rule = '', $field = '', $value = null, $message = null): void
    {
        $this->validateHandleErrorsInArray(
            ValidateFile::validateFileUploadMandatory($field, $value, $message),
            $field
        );
    }

    protected function validateMaximumFileNumbers($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);

        if (!is_numeric($rule) || ($rule < 0)) {
            $text = "O parâmetro do validador 'maxFile', deve ser numérico e maior ou igual a zero!";
            $text = !empty($message) ? $message : $text;
            $this->errors[$field] = $text;
            return;
        }

        $validateResult = ValidateFile::validateMaximumFileNumbers($rule, $field, $value, $message);
        $this->validateHandleErrorsInArray($validateResult, $field);
    }

    protected function validateMinimumFileNumbers($rule = '', $field = '', $value = null, $message = null): void
    {
        $rule = trim($rule);

        if (!is_numeric($rule) || ($rule < 0)) {
            $text = "O parâmetro do validador 'minFile', deve ser numérico e maior ou igual a zero!";
            $text = !empty($message) ? $message : $text;
            $this->errors[$field] = $text;
            return;
        }

        $validateResult = ValidateFile::validateMinimumFileNumbers($rule, $field, $value, $message);
        $this->validateHandleErrorsInArray($validateResult, $field);
    }
}
