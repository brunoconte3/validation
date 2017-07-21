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
        foreach($rules as $ruleKey => $ruleValue){
            if(isset($datas[$ruleKey])){
                $this->rules($datas[$ruleKey], $ruleKey, $ruleValue);
            }
        }
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Definindo mais de uma regra para um determinado dado.
     * -------------------------------------------------------------------------
     * 
     * @param type $data
     * @param type $ruleKey
     * @param type $ruleValue
     */
    
    
    private function rules($data, $ruleKey, $ruleValue) {
        $conditions = explode('|', $ruleValue);
        
        foreach($conditions as $condition){
            $this->validate($condition, $data, $ruleKey);
        }
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Regras de validação para os dados.
     * ------------------------------------------------------------------------- 
     * 
     * @param type $condition
     * @param type $data
     * @param type $ruleKey
     */
    
    
    private function validate($condition, $data, $ruleKey) {
        $subitem = explode(':', $condition);
        
        switch($subitem[0]){
            case 'required':
                if(empty($data) || $data == '' || $data == ' '){
                    $this->erros["$ruleKey"] = "O campo {$ruleKey} é obrigatório.";
                }
            break;
            case 'max':
                if(strlen($data) > $subitem[1]){
                    $this->erros["$ruleKey"] = "O campo {$ruleKey} precisa conter no máximo {$subitem[1]} caracteres.";
                }
            break;
            case 'min':
                if(strlen($data) < $subitem[1]){
                    $this->erros["$ruleKey"] = "O campo {$ruleKey} precisa conter no mínimo {$subitem[1]} caracteres.";
                }
            break;
            case 'email':
                if(!filter_var($data, FILTER_VALIDATE_EMAIL)){
                    $this->erros["$ruleKey"] = "O campo {$ruleKey} é necessário que seja um email válido.";
                }
            break;
            case 'url':
                if(!filter_var($data, FILTER_VALIDATE_URL)){
                    $this->erros["$ruleKey"] = "O campo {$ruleKey} é necessário que seja uma URL válido.";
                }
            break;
            case 'numeric':
                if(!is_numeric($data)){
                    $this->erros["$ruleKey"] = "O campo {$ruleKey} só pode conter valores numéricos.";
                }
            break;
            case 'float':
                if(!filter_var($data, FILTER_VALIDATE_FLOAT)){
                    $this->erros["$ruleKey"] = "O campo {$ruleKey} deve ser do tipo real.";
                }
            break;
            case 'int':
                if(!filter_var($data, FILTER_VALIDATE_INT)){
                    $this->erros["$ruleKey"] = "O campo {$ruleKey} deve ser do tipo inteiro.";
                }
            break;
            case 'regex':
                if(!preg_match($subitem[1], $data) !== FALSE){
                    $this->erros["$ruleKey"] = "O campo {$ruleKey} deve corresponder com as expecificações requisitadas.";
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

