<?php

namespace Mammoth\Validation;


class Validator {
    
    
    /**
     * @var type 
     */
    
    
    private $erros = FALSE;
    
    
    /**
     * @var type 
     */
    
    
    private $lang  = [];
    
    
    /**
     * -------------------------------------------------------------------------
     * Define a lingua para tradução das mensagens
     * -------------------------------------------------------------------------
     * 
     * @param type $lang
     * @throws \Exception
     */
    
    
    public function __construct($lang = 'pt-br') {
        $lang_file = __DIR__ . '/../../languages/' . $lang . '.php';
        
        if(file_exists($lang_file)):
            $this->lang = require $lang_file;
        else:
            throw new \Exception("Language with key $lang does not exist");
        endif;
        
    }


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
                    $this->erros[$rule_key] = $message[1] ?? $this->messageTranslated('required', $rule_key); //"O campo $rule_key é obrigatório.";
                }
            break;
            case 'max':
                if(strlen($data_value) > $item[1]){
                    $this->erros[$rule_key] = $message[1] ?? $this->messageTranslated('max', $rule_key, $item[1]);
                }
            break;
            case 'min':
                if(strlen($data_value) < $item[1]){
                    $this->erros[$rule_key] = $message[1] ?? $this->messageTranslated('min', $rule_key, $item[1]); 
                }
            break;
            case 'bool':
                if(!filter_var($data_value, FILTER_VALIDATE_BOOLEAN)){
                    $this->erros[$rule_key] = $message[1] ?? $this->messageTranslated('bool', $rule_key); 
                }
            break;
            case 'email':
                if(!filter_var($data_value, FILTER_VALIDATE_EMAIL)){
                    $this->erros[$rule_key] = $message[1] ?? $this->messageTranslated('email', $rule_key);
                }
            break;
            case 'float':
                if(!filter_var($data_value, FILTER_VALIDATE_FLOAT)){
                    $this->erros[$rule_key] = $message[1] ?? $this->messageTranslated('float', $rule_key); 
                }
            break;
            case 'int':
                if(!filter_var($data_value, FILTER_VALIDATE_INT)){
                    $this->erros[$rule_key] = $message[1] ?? $this->messageTranslated('int', $rule_key);
                }
            break;
            case 'ip':
                if(!filter_var($data_value, FILTER_VALIDATE_IP)){
                    $this->erros[$rule_key] = $message[1] ?? $this->messageTranslated('ip', $rule_key);
                }
            break;
            case 'mac':
                if(!filter_var($data_value, FILTER_VALIDATE_MAC)){
                    $this->erros[$rule_key] = $message[1] ?? $this->messageTranslated('mac', $rule_key);
                }
            break;
            case 'numeric':
                if(!is_numeric($data_value)){
                    $this->erros[$rule_key] = $message[1] ?? $this->messageTranslated('numeric', $rule_key);
                }
            break;
            case 'regex':
                if(!preg_match($item[1], $data_value) !== FALSE){
                    $this->erros[$rule_key] = $message[1] ?? $this->messageTranslated('regex', $rule_key);
                }
            break;
            case 'url':
                if(!filter_var($data_value, FILTER_VALIDATE_URL)){
                    $this->erros[$rule_key] = $message[1] ?? $this->messageTranslated('url', $rule_key); 
                }
            break;
        }
    }

    
    /**
     * -------------------------------------------------------------------------
     * Traduz as mensagens de cada regra para o idioma informado.
     * -------------------------------------------------------------------------
     * 
     * @param type $rule
     * @param type $field
     * @param type $value
     * @return type
     */
    
    
    private function messageTranslated($rule, $field, $value = NULL) {
        $message = str_replace('{$field}', $field, $this->lang[$rule]);
        
        return str_replace('{$value}', $value, $message);
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
