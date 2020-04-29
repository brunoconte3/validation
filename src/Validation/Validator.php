<?php

namespace brunoconte3\Validation;

class Validator
{
    private $erros = false;

    public function set(array $datas, array $rules)
    {
        foreach ($rules as $ruleKey => $ruleValue) {
            if (isset($datas[$ruleKey])) {
                $this->rules($datas[$ruleKey], $ruleKey, $ruleValue);
            }
        }
    }

    private function rules($dataValue, $ruleKey, $ruleValue)
    {
        $conditions = explode('|', $ruleValue);

        foreach ($conditions as $condition) {
            if (!isset($this->erros[$ruleKey])) {
                $this->validate($condition, $dataValue, $ruleKey);
            }
        }
    }

    private function validate($condition, $dataValue, $ruleKey)
    {
        $message = explode(',', $condition);
        $item    = explode(':', $message[0]);

        switch ($item[0]) {
            case 'required':
                if (empty($dataValue) || $dataValue == '' || $dataValue == ' ') {
                    $this->erros[$ruleKey] = $message[1] ?? "O campo $ruleKey é obrigatório!";
                }
                break;
            case 'max':
                if (strlen($dataValue) > $item[1]) {
                    $this->erros[$ruleKey] = $message[1]
                        ?? "O campo $ruleKey precisa conter no máximo $item[1] caracteres!";
                }
                break;
            case 'min':
                if (strlen($dataValue) < $item[1]) {
                    $this->erros[$ruleKey] = $message[1] ??
                        "O campo $ruleKey precisa conter no mínimo $item[1] caracteres!";
                }
                break;
            case 'alpha':
                if (
                    !preg_match(
                        '/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/',
                        $dataValue
                    ) !== false
                ) {
                    $this->erros[$ruleKey] = $message[1] ?? "O campo $ruleKey só pode conter caracteres alfabéticos!";
                }
                break;
            case 'alnum':
                if (
                    !preg_match(
                        '/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/',
                        $dataValue
                    ) !== false
                ) {
                    $this->erros[$ruleKey] = $message[1] ?? "O campo $ruleKey deve conter caracteres alfanuméricos!";
                }
                break;
            case 'bool':
                if (!filter_var($dataValue, FILTER_VALIDATE_BOOLEAN)) {
                    $this->erros[$ruleKey] = $message[1] ??
                        "O campo $ruleKey só pode conter valores lógicos. (true|false, 1|0, yes|no)!";
                }
                break;
            case 'email':
                if (!filter_var($dataValue, FILTER_VALIDATE_EMAIL)) {
                    $this->erros[$ruleKey] = $message[1] ?? "O campo $ruleKey deve ser um endereço de email válido!";
                }
                break;
            case 'float':
                if (!filter_var($dataValue, FILTER_VALIDATE_FLOAT)) {
                    $this->erros[$ruleKey] = $message[1] ?? "O campo $ruleKey deve ser do tipo real(flutuante)!";
                }
                break;
            case 'identifier':
                if (!preg_match('/^([0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2})+$/', $dataValue) !== false) {
                    $this->erros[$ruleKey] = $message[1] ??
                        "O campo $ruleKey deve corresponder ao formato 000.000.000-00!";
                }
                break;
            case 'int':
                if (!filter_var($dataValue, FILTER_VALIDATE_INT)) {
                    $this->erros[$ruleKey] = $message[1] ?? "O campo $ruleKey deve ser do tipo inteiro!";
                }
                break;
            case 'ip':
                if (!filter_var($dataValue, FILTER_VALIDATE_IP)) {
                    $this->erros[$ruleKey] = $message[1] ?? "O campo $ruleKey deve ser um endereço de IP válido!";
                }
                break;
            case 'mac':
                if (!filter_var($dataValue, FILTER_VALIDATE_MAC)) {
                    $this->erros[$ruleKey] = $message[1] ?? "O campo $ruleKey deve ser um endereço de MAC válido!";
                }
                break;
            case 'numeric':
                if (!is_numeric($dataValue)) {
                    $this->erros[$ruleKey] = $message[1] ?? "O campo $ruleKey só pode conter valores numéricos!";
                }
                break;
            case 'phone':
                if (!preg_match('/^(\([0-9]{2}\)[0-9]{4}-[0-9]{4})+$/', $dataValue) !== false) {
                    $this->erros[$ruleKey] = $message[1] ??
                        "O campo $ruleKey deve corresponder ao formato (00)0000-0000!";
                }
                break;
            case 'plate':
                if (!preg_match('/^[A-Z]{3}-[0-9]{4}+$/', $dataValue) !== false) {
                    $this->erros[$ruleKey] = $message[1] ?? "O campo $ruleKey deve corresponder ao formato AAA-0000!";
                }
                break;
            case 'regex':
                if (!preg_match($item[1], $dataValue) !== false) {
                    $this->erros[$ruleKey] = $message[1]
                        ?? "O campo $ruleKey precisa conter um valor com formato válido!";
                }
                break;
            case 'url':
                if (!filter_var($dataValue, FILTER_VALIDATE_URL)) {
                    $this->erros[$ruleKey] = $message[1] ?? "O campo $ruleKey deve ser um endereço de URL válida!";
                }
                break;
            case 'zip_code':
                if (!preg_match('/^([0-9]{2}\.[0-9]{3}-[0-9]{3})+$/', $dataValue) !== false) {
                    $this->erros[$ruleKey] = $message[1] ?? "O campo $ruleKey deve corresponder ao formato 00.000-000";
                }
                break;
        }
    }

    public function getErros()
    {
        return $this->erros;
    }
}
