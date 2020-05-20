<?php

namespace brunoconte3\Validation;

use brunoconte3\Validation\{
    ValidateCpf,
    ValidateCnpj,
    ValidatePhone,
    ValidateHour,
};

class Rules
{
    protected $errors = false;

    public static function functionsValidatade(): array
    {
        return [
            'required' => 'validateFieldMandatory',
            'type' => 'validateFieldType',
            'min' => 'validateMinimumField',
            'max' => 'validateMaximumField',
            'alpha' => 'validateAlphabets',
            'alnum' => 'validateAlphaNumerics',
            'alphaNum' => 'validateAlphabetsNum',
            'array' => 'validateArray',
            'bool' => 'validateBoolean',
            'companyIdentification' => 'validateCompanyIdentification',
            'companyIdentificationMask' => 'validateCompanyIdentificationMask',
            'dateBrazil' => 'validateDateBrazil',
            'dateAmerican' => 'validateDateAmerican',
            'email' => 'validateEmail',
            'float' => 'validateFloating',
            'hour' => 'validateHour',
            'identifier' => 'validateIdentifier',
            'identifierMask' => 'validateIdentifierMask',
            'int' => 'validateInteger',
            'ip' => 'validateIp',
            'mac' => 'validateMac',
            'numeric' => 'validateNumeric',
            'numMax' => 'validateNumMax',
            'numMin' => 'validateNumMin',
            'phone' => 'validatePhone',
            'plate' => 'validatePlate',
            'regex' => 'validateRegex',
            'url' => 'validateUrl',
            'noWeekend' => 'validateWeekend',
            'zipcode' => 'validateZipCode',
        ];
    }

    protected function validateFieldMandatory($rule = '', $field = '', $value = null, $message = null)
    {
        if (empty(trim($value))) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field é obrigatório!";
        }
    }

    protected function validateFieldType($rule = '', $field = '', $value = null, $message = null)
    {
        $method = trim(self::functionsValidatade()[trim(strtolower($rule))] ?? 'invalidRule');
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
        //essa função serve para validar dados com json_encode multiplos, e indices quebrados na estrutura
        foreach ($data as $key => $val) {
            if (is_string($val) && !empty($val)) {
                $arr = json_decode($val, true);
                if (is_array($arr) && (json_last_error() === JSON_ERROR_NONE)) {
                    $val = $arr;
                }
            }
            if (is_array($val)) {
                $data[$key] = $this->levelSubLevelsArrayReturnJson($val, true);
            } elseif (is_string($val)) {
                $data[$key] =  addslashes($val);
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
                    foreach ($rulesConf as $valueRuleConf) {
                        $conf = explode(',', trim($valueRuleConf));
                        $ruleArrayConf = explode(':', $conf[0] ?? '');
                        $rulesArray['mensagem'] = trim($conf[1] ?? $rulesArray['mensagem'] ?? null);
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
            $rulesArray = !empty($rulesArray) && is_array($rulesArray) ? $rulesArray : [];
            //irá chamar uma função para cada validação no json de validação, passando o valor para a função
            $msgCustomized = $rulesArray['mensagem'] ?? null;
            if (array_key_exists('mensagem', $rulesArray)) {
                unset($rulesArray['mensagem']);
            }
            foreach ($rulesArray as $key => $val) {
                $method = trim(Rules::functionsValidatade()[trim($key)] ?? 'invalidRule');
                $call = [$this, $method];
                //chama a função de validação, de cada parametro json
                if (is_callable($call, true, $method)) {
                    call_user_func_array($call, [$val, $field, $value, $msgCustomized]);
                } else {
                    $this->errors[$field] = "Há regras de validação não implementadas no campo $field!";
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
                    $this->errors[$field] = "Há regras de validação não implementadas no campo $field!";
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
                '/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/',
                $value
            ) !== false
        ) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter caracteres alfabéticos!";
        }
    }

    protected function validateAlphabetsNum($rule = '', $field = '', $value = null, $message = null)
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
                '/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/',
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

    protected function validateBoolean($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter valores lógicos. (true, 1, yes)!";
        }
    }

    protected function validateCompanyIdentification($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ValidateCnpj::validateCnpj($value, false)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field é inválido!";
        }
    }

    protected function validateCompanyIdentificationMask($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ValidateCnpj::validateCnpj($value, true)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field é inválido!";
        }
    }

    protected function validateDateBrazil($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ValidateDate::validateDateBrazil($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é uma data válida!";
        }
    }

    protected function validateDateAmerican($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ValidateDate::validateDateAmerican($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é uma data válida!";
        }
    }

    protected function validateEmail($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser um endereço de email válido!";
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
        if (!ValidateCpf::validateCpf($value, false)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field é inválido!";
        }
    }

    protected function validateIdentifierMask($rule = '', $field = '', $value = null, $message = null)
    {

        if (!ValidateCpf::validateCpf($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field é inválido!";
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

    protected function validateMac($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_MAC)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser um endereço de MAC válido!";
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

    protected function validateNumMin($rule = '', $field = '', $value = null, $message = null)
    {
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

    protected function validateZipCode($rule = '', $field = '', $value = null, $message = null)
    {
        if (!preg_match('/^([0-9]{2}[0-9]{3}-[0-9]{3})+$/', $value) !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve corresponder ao formato 00000-000!";
        }
    }

    protected function validatePhone($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ValidatePhone::validate($value)) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field não é um telefone válido!";
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

    protected function invalidRule($rule = '', $field = '', $value = null, $message = null)
    {
        $msg = "Uma regra inválida está sendo aplicada no campo $field!";
        $this->errors[$field] = $msg;
    }
}
