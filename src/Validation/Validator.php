<?php

namespace Mammoth\Validation;


class Validator {
    
    
    /**
     * @var type 
     */
    
    
    private $erros = FALSE;


    /**
     * -------------------------------------------------------------------------
     * Setando/Definindo regras para determinados dados.
     * -------------------------------------------------------------------------
     * 
     * @param array $datas
     * @param array $rules
     */
    
    
    public function set(array $datas, array $rules) {
        foreach($rules as $rule_key => $rule_value){
            if(isset($datas[$rule_key])){
                $this->rules($datas[$rule_key], $rule_key, $rule_value);
            }
        }
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Definindo mais de uma regra para um determinado dado.
     * -------------------------------------------------------------------------
     * 
     * @param type $data_value
     * @param type $rule_key
     * @param type $rule_value
     */
    
    
    private function rules($data_value, $rule_key, $rule_value) {
        $conditions = explode('|', $rule_value);
        
        foreach($conditions as $condition){
            if(!isset($this->erros[$rule_key])){
                $this->validate($condition, $data_value, $rule_key);
            }
        }
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Regras de validação para os dados.
     * ------------------------------------------------------------------------- 
     * 
     * @param type $condition
     * @param type $data_value
     * @param type $rule_key
     */
    
    
    private function validate($condition, $data_value, $rule_key) {
        $message = explode(',', $condition);
        $item    = explode(':', $message[0]);
        
        switch($item[0]){
            case 'required':
                if(empty($data_value) || $data_value == '' || $data_value == ' '){
                    $this->erros[$rule_key] = $message[1] ?? "O campo $rule_key é obrigatório.";
                }
            break;
            case 'max':
                if(strlen($data_value) > $item[1]){
                    $this->erros[$rule_key] = $message[1] ?? "O campo $rule_key precisa conter no máximo $item[1] caracteres.";
                }
            break;
            case 'min':
                if(strlen($data_value) < $item[1]){
                    $this->erros[$rule_key] = $message[1] ?? "O campo $rule_key precisa conter no mínimo $item[1] caracteres."; 
                }
            break;
            case 'bool':
                if(!filter_var($data_value, FILTER_VALIDATE_BOOLEAN)){
                    $this->erros[$rule_key] = $message[1] ?? "O campo $rule_key só pode conter valores lógicos. (true|false, 1|0, yes|no)."; 
                }
            break;
            case 'email':
                if(!filter_var($data_value, FILTER_VALIDATE_EMAIL)){
                    $this->erros[$rule_key] = $message[1] ?? "O campo $rule_key deve ser um endereço de email válido.";
                }
            break;
            case 'float':
                if(!filter_var($data_value, FILTER_VALIDATE_FLOAT)){
                    $this->erros[$rule_key] = $message[1] ?? "O campo $rule_key deve ser do tipo real(flutuante)."; 
                }
            break;
            case 'int':
                if(!filter_var($data_value, FILTER_VALIDATE_INT)){
                    $this->erros[$rule_key] = $message[1] ?? "O campo $rule_key deve ser do tipo inteiro.";
                }
            break;
            case 'ip':
                if(!filter_var($data_value, FILTER_VALIDATE_IP)){
                    $this->erros[$rule_key] = $message[1] ?? "O campo $rule_key deve ser um endereço de IP válido.";
                }
            break;
            case 'mac':
                if(!filter_var($data_value, FILTER_VALIDATE_MAC)){
                    $this->erros[$rule_key] = $message[1] ?? "O campo $rule_key deve ser um endereço de MAC válido.";
                }
            break;
            case 'numeric':
                if(!is_numeric($data_value)){
                    $this->erros[$rule_key] = $message[1] ?? "O campo $rule_key só pode conter valores numéricos.";
                }
            break;
            case 'regex':
                if(!preg_match($item[1], $data_value) !== FALSE){
                    $this->erros[$rule_key] = $message[1] ?? "O campo $rule_key precisa conter um valor com formato válido.";
                }
            break;
            case 'url':
                if(!filter_var($data_value, FILTER_VALIDATE_URL)){
                    $this->erros[$rule_key] = $message[1] ?? "O campo $rule_key deve ser um endereço de URL válida."; 
                }
            break;
        }
    }

    
    /**
     * -------------------------------------------------------------------------
     * Retorna todos os erros possíveis.
     * -------------------------------------------------------------------------
     * 
     * @return type
     */
    
    
    public function getErros() {
        return $this->erros;
    }
    
} 
