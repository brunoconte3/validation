<?php

namespace brunoconte3\Validation;

use brunoconte3\Validation\{
    ValidateCpf,
    ValidateCnpj,
    ValidatePhone,
    ValidateHour,
};

class Validator
{
    private $errors = false;
    private const FUNCTIONS = [
        'required' => 'validateFieldMandatory',
        'type' => 'validateFieldType',
        'min' => 'validateMinimumField',
        'max' => 'validateMaximumField',
        'alpha' => 'validateAlphabets',
        'alnum' => 'validateAlphaNumerics',
        'bool' => 'validateBoolean',
        'email' => 'validateEmail',
        'float' => 'validateFloating',
        'identifier' => 'validateIdentifier',
        'identifierMask' => 'validateIdentifierMask',
        'companyIdentification' => 'validateCompanyIdentification',
        'companyIdentificationMask' => 'validateCompanyIdentificationMask',
        'int' => 'validateInteger',
        'ip' => 'validateIp',
        'mac' => 'validateMac',
        'numeric' => 'validateNumeric',
        'plate' => 'validatePlate',
        'regex' => 'validateRegex',
        'url' => 'validateUrl',
        'zipcode' => 'validateZipCode',
        'phone' => 'validatePhone',
        'dateBrazil' => 'validateDateBrazil',
        'hour' => 'validateHour',
    ];

    public function set(array $dates, array $rules)
    {
        //prepara dados para validação
        $dates = json_decode($this->levelSubLevelsArrayReturnJson($dates), true);
        if (empty($dates)) {
            $this->errors['erro'] = "informe os dados!";
            return false;
        }
        //se for uma lista, valida a lista de objetos
        if (count(array_filter(array_keys($dates), 'is_numeric')) == count($dates)) {
            foreach ($dates as $val) {
                $this->validateSubLevelData($val, $rules, true);
            }
        } else {
            $this->validateSubLevelData($dates, $rules, true);
        }
        return true;
    }

    public function getErros()
    {
        return $this->errors;
    }

    private function levelSubLevelsArrayReturnJson(array $data, bool $recursive = false)
    {
        //funcao recurssiva para tratar array e retornar json valido
        //essa funcação server para validar dados com json_encode multiplos, e indicesquebrados na estrutura
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
            //se for recurssivo retorna array
            return $data;
        }
        //se for raiz retorna json
        return strtr(stripslashes(json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        )), ["\r" => '', "\n" => '', "\t" => '']);
    }

    private function validateSubLevelData(
        array $data,
        array $rules
    ) {
        //precorre o array de validação para não rodar recurssivamente atoa
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
            //validação camo a campo
            if (is_string($val)) {
                $this->validateRuleField($key, ($data[$key] ?? null), $val, array_key_exists($key, $data));
            }
        }
        return $rules;
    }

    private function validateRuleField($field, $value, $rules, $valid = false)
    {
        //se o campo é valido, ele exite no json de dados, no mesmo nivel que a regra
        if ($valid) {
            //transforma a string json de validação em array para validação
            $rulesArray = is_array($rules) ? $rules : [];
            if (is_string($rules) && !empty($rules)) {
                $rulesArray = json_decode($rules, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $rulesArray = [];
                    //--------------------------------------------------
                    //suporte ao padrão antigo
                    //'int|required|min:14|max:14',
                    $rulesConf = explode('|', trim($rules));
                    foreach ($rulesConf as $valueRuleConf) {
                        $ruleArrayConf = explode(':', trim($valueRuleConf));
                        $msgCustomized = explode(',', trim($valueRuleConf));
                        $rulesArray['mensagem'] = $msgCustomized[1] ?? $rulesArray['mensagem'] ?? null;
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
                $method = trim(self::FUNCTIONS[trim($key)] ?? 'invalidRule');
                $call = [$this, $method];
                //chama há função de validação, de cada parametro json
                if (is_callable($call, true, $method)) {
                    call_user_func_array($call, [$val, $field, $value, $msgCustomized]);
                } else {
                    $this->errors[$field] = "Há regras de validação não implementadas no campo $field!";
                }
            }
        } else {
            //se o campo é invalido, ele não exite no json de dados no mesmo nivel que a regra
            //aqui valida se na regra há filhos obrigatorios para esse campo
            $rulesArray = is_array($rules) ? $rules : [];
            if (is_string($rules) && !empty($rules)) {
                $rulesArray = json_decode($rules, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $rulesArray = [];
                    //--------------------------------------------------
                    //suporte ao padrão antigo
                    //'int|required|min:14|max:14',
                    $rulesConf = explode('|', trim($rules));
                    foreach ($rulesConf as $valueRuleConf) {
                        $ruleArrayConf =  explode(':', trim($valueRuleConf));
                        if (!empty($ruleArrayConf)) {
                            $rulesArray[$ruleArrayConf[0] ?? (count($rulesArray) + 1)] = $ruleArrayConf[1] ?? true;
                        }
                    }
                    //--------------------------------------------------
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

    private function validateFieldMandatory($rule = '', $field = '', $value = null, $message = null)
    {
        if (empty(trim($value))) {
            $this->errors[$field] = !empty($message) ? $message : "O campo $field é obrigatório!";
        }
    }

    private function validateFieldType($rule = '', $field = '', $value = null, $message = null)
    {
        $method = trim(self::FUNCTIONS[trim(strtolower($rule))] ?? 'invalidRule');
        $call = [$this, $method];
        //chama há função de validação, de cada parametro json
        if (is_callable($call, true, $method)) {
            call_user_func_array($call, [$rule, $field, $value, $message]);
        } else {
            $this->errors[$field] = "Há regras de validação não implementadas no campo $field!";
        }
    }
    private function validateMinimumField($rule = '', $field = '', $value = null, $message = null)
    {
        if (strlen($value) < $rule) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field precisa conter no mínimo $rule caracteres!";
        }
    }
    private function validateMaximumField($rule = '', $field = '', $value = null, $message = null)
    {
        if (strlen($value) > $rule) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field precisa conter no máximo $rule caracteres!";
        };
    }
    private function validateAlphabets($rule = '', $field = '', $value = null, $message = null)
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
    private function validateAlphaNumerics($rule = '', $field = '', $value = null, $message = null)
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

    private function validateBoolean($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter valores lógicos. (true, 1, yes)!";
        }
    }
    private function validateEmail($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser um endereço de email válido!";
        }
    }
    private function validateFloating($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser do tipo real(flutuante)!";
        }
    }
    private function validateIdentifier($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ValidateCpf::validateCpf($value, false)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field é inválido!";
        }
    }
    private function validateIdentifierMask($rule = '', $field = '', $value = null, $message = null)
    {

        if (!ValidateCpf::validateCpf($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field é inválido!";
        }
    }
    private function validateCompanyIdentification($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ValidateCnpj::validateCnpj($value, false)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field é inválido!";
        }
    }
    private function validateCompanyIdentificationMask($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ValidateCnpj::validateCnpj($value, true)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field é inválido!";
        }
    }
    private function validateInteger($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser do tipo inteiro!";
        }
    }
    private function validateIp($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_IP)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser um endereço de IP válido!";
        }
    }
    private function validateMac($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_MAC)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser um endereço de MAC válido!";
        }
    }
    private function validateNumeric($rule = '', $field = '', $value = null, $message = null)
    {
        if (!is_numeric($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field só pode conter valores numéricos!";
        }
    }
    private function validatePlate($rule = '', $field = '', $value = null, $message = null)
    {
        if (!preg_match('/^[A-Z]{3}-[0-9]{4}+$/', $value) !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve corresponder ao formato AAA-0000!";
        }
    }

    private function validateRegex($rule = '', $field = '', $value = null, $message = null)
    {
        if (!preg_match($rule, $value) !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field precisa conter um valor com formato válido!";
        }
    }
    private function validateUrl($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser um endereço de URL válida!";
        }
    }
    private function validateZipCode($rule = '', $field = '', $value = null, $message = null)
    {
        if (!preg_match('/^([0-9]{2}[0-9]{3}-[0-9]{3})+$/', $value) !== false) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve corresponder ao formato 00000-000!";
        }
    }
    private function validatePhone($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ValidatePhone::validate($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é um telefone válido!";
        }
    }

    private function validateDateBrazil($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ValidateDate::validateDateBrazil($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é uma data válida!";
        }
    }
    private function validateHour($rule = '', $field = '', $value = null, $message = null)
    {
        if (!ValidateHour::validateHour($value)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field não é uma hora válida!";
        }
    }

    private function invalidRule($rule = '', $field = '', $value = null, $message = null)
    {
        $msg = "Uma regra inválida está sendo aplicada no campo $field!";
        $this->errors[$field] = $msg;
    }
}
