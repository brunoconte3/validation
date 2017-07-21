<?php

namespace Validator;


class Validator {
    
    private $erros = false;

    public function __construct(array $data, array $rules){
        foreach($rules as $ruleKey => $ruleValue):
            if(isset($data[$ruleKey])):
                $this->rules($data[$ruleKey], $ruleKey, $ruleValue);
            endif;
        endforeach;
    }
    
    private function rules($data, $ruleKey, $ruleValue){
        $conditons = explode('|', $ruleValue);
        foreach($conditons as $condition):
            $this->valid($condition, $data, $ruleKey);
        endforeach;
    }
    
    private function valid($condition, $data, $ruleKey){
        $subitem = explode(':', $condition);
        switch($subitem[0]):
            case 'required':
                if(empty($data) || $data == '' || $data == ' '):
                    $this->erros[] = "O campo {$ruleKey} nâo foi preechido.";
                endif;
            break;
            case 'max':
                if(strlen($data) > $subitem[1]):
                    $this->erros[] = "O campo {$ruleKey} excedeu ao limit maximo de {$subitem[1]}.";
                endif;
            break;
            case 'min':
                if(strlen($data) < $subitem[1]):
                    $this->erros[] = "O campo {$ruleKey} é inferior ao limit minimo de {$subitem[1]}.";
                endif;
            break;
            case 'email':
                if(!filter_var($data, FILTER_VALIDATE_EMAIL)):
                    $this->erros[] = "O campo {$ruleKey} deve atender as expecificações de email.";
                endif;
            break;
            case 'float':
                if(!filter_var($data, FILTER_VALIDATE_FLOAT)):
                    $this->erros[] = "O campo {$ruleKey} deve ser do tipo real.";
                endif;
            break;
            case 'int':
                if(!filter_var($data, FILTER_VALIDATE_INT)):
                    $this->erros[] = "O campo {$ruleKey} deve ser do tipo inteiro.";
                endif;
            break;
            case 'regex':
                if(preg_match($subitem[1], $data) !== FALSE):
                    $this->erros[] = "O campo {$ruleKey} deve cooresponder com as expecificações requisitadas.";
                endif;
            break;
        endswitch;
    }

        public function getErros(){
        return $this->erros;
    }
    
} 

