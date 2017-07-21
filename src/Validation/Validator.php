<?php

namespace Mammoth\Validation;


class Validator {
    
    
    private $erros = false;
    
    private $data, $rules;

    public function set(array $data, array $rules){
        $this->data  = $data;
        $this->rules = $rules; 
    }

    public function validate(){
        foreach($this->rules as $ruleKey => $ruleValue){
            if(isset($this->data[$ruleKey])){
                $this->rules($ruleKey, $ruleValue);
            }
        }
    }
    
    
    private function rules($ruleKey, $ruleValue){
        $conditions = explode('|', $ruleValue);
        
        foreach($conditions as $condition){
            $this->run($condition, $ruleKey);
        }
    }
    
    
    private function run($condition, $ruleKey){
        $subitem = explode(':', $condition);
        
        switch($subitem[0]){
            case 'required':
                if(empty($this->data[$ruleKey]) || $this->data[$ruleKey] == '' || $this->data[$ruleKey] == ' '){
                    $this->erros[] = "O campo {$ruleKey} é obrigatório.";
                }
            break;
            case 'max':
                if(strlen($this->data[$ruleKey]) > $subitem[1]){
                    $this->erros[] = "O campo {$ruleKey} precisa conter no máximo {$subitem[1]} caracteres.";
                }
            break;
            case 'min':
                if(strlen($this->data[$ruleKey]) < $subitem[1]){
                    $this->erros[] = "O campo {$ruleKey} precisa conter no mínimo {$subitem[1]} caracteres.";
                }
            break;
            case 'email':
                if(!filter_var($this->data[$ruleKey], FILTER_VALIDATE_EMAIL)){
                    $this->erros[] = "O campo {$ruleKey} é necessário que seja um email válido.";
                }
            break;
            case 'url':
                if(!filter_var($this->data[$ruleKey], FILTER_VALIDATE_URL)){
                    $this->erros[] = "O campo {$ruleKey} é necessário que seja uma URL válido.";
                }
            break;
            case 'numeric':
                if(!is_numeric($this->data[$ruleKey])){
                    $this->erros[] = "O campo {$ruleKey} só pode conter valores numéricos.";
                }
            break;
            case 'float':
                if(!filter_var($this->data[$ruleKey], FILTER_VALIDATE_FLOAT)){
                    $this->erros[] = "O campo {$ruleKey} deve ser do tipo real.";
                }
            break;
            case 'int':
                if(!filter_var($this->data[$ruleKey], FILTER_VALIDATE_INT)){
                    $this->erros[] = "O campo {$ruleKey} deve ser do tipo inteiro.";
                }
            break;
            case 'regex':
                if(preg_match($subitem[1], $this->data[$ruleKey]) !== FALSE){
                    $this->erros[] = "O campo {$ruleKey} deve corresponder com as expecificações requisitadas.";
                }
            break;
        }
    }

    
    public function getErros(){
        return $this->erros;
    }
    
} 

