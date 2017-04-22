<?php

namespace Creed\Validation;


class Validator {
    
    /** @var type private|static $instance */
    private static $instance           = NULL;

    /** @var type private|static $instance */
    private static $fields             = array();

    /** @var type private|static $instance */
    private static $validation_methods = array();

    /** @var type private $instance */
    private $validation_rules          = array();
   
    /** @var type private $instance */
    private $errors                    = array();
    
    
    /**
     * -------------------------------------------------------------------------
     * Função para criar e retornar instância
     * -------------------------------------------------------------------------
     * 
     * @return Validator
     */
    
    private static function getInstance() {
        
        if(self::$instance === NULL):
            self::$instance = new self();
        endif;
        
        return self::$instance;
    }

    
    /**
     * -------------------------------------------------------------------------
     * Método de Taquigrafia para validação em linha.
     * -------------------------------------------------------------------------
     * 
     * @param array $datas
     * @param array $validators
     * @return boolean
     */
    
    public static function make(array $datas, array $validators) {
        $validator = self::getInstance();

        $validator->validationRules($validators);

        if ($validator->dispatch($datas) === FALSE):
            return $validator->fails(FALSE);
        else:
            return TRUE;
        endif;
    }


    /**
     * -------------------------------------------------------------------------
     * Getter/Setter para as regras de validação.
     * -------------------------------------------------------------------------
     * 
     * @param array $rules
     * @return type
     */
    
    private function validationRules(array $rules = array()) {
        
        if (empty($rules)):
            return $this->validation_rules;
        endif;

        $this->validation_rules = $rules;
    }

   
    /**
     * -------------------------------------------------------------------------
     * Executa a filtragem e validação um após o outro.
     * -------------------------------------------------------------------------
     * 
     * @param array $datas
     * @param type $verify_fields
     * @return boolean|array
     */
    
    private function dispatch(array $datas, $verify_fields = FALSE) {

        $validated = $this->validate($datas, $this->validationRules());
 
        if ($verify_fields === TRUE):
            $this->verifyFields($datas);
        endif;

        if ($validated !== TRUE):
            return FALSE;
        endif;

        return $datas;
    }

    
    /**
     * -------------------------------------------------------------------------
     * Certifique-se de que as contagens dos campos coincidem com as contagens 
     * das regras de validação.
     * -------------------------------------------------------------------------
     * 
     * @param array $datas
     */
    
    private function verifyFields(array $datas) {
        $set_rules = $this->validationRules();
        $mismatch = array_diff_key($datas, $set_rules);
        $fields = array_keys($mismatch);

        foreach ($fields as $field) {
            $this->errors[] = array(
                'field' => $field,
                'value' => $datas[$field],
                'rule'  => 'mismatch',
                'param' => NULL,
            );
        }
    }

   
    /**
     * -------------------------------------------------------------------------
     * Realiza a validação de dados contra o conjunto de regras fornecido.
     * -------------------------------------------------------------------------
     * 
     * @param array $input
     * @param array $set_rules
     * @return type
     * @throws Exception
     */


    private function validate(array $input, array $set_rules) {

        $look_for = array('required', 'required_file');
        
        foreach ($set_rules as $field => $rules):

            $rules = explode('|', $rules);

            if (count(array_intersect($look_for, $rules)) > 0 || (isset($input[$field]) && !is_array($input[$field]))):
                $this->rumRules($rules, $field, $input);
            endif;
        endforeach;

        return (count($this->errors) > 0) ? $this->errors : TRUE;
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Determina como a(s) função(ões) deve ser executada com a passagem de 
     * parâmetro
     * -------------------------------------------------------------------------
     *  
     * @param type $method
     * @param type $field
     * @param type $input
     * @param type $param
     */
    
    private function callFunc($method, $field, $input, $param){
        $result = $this->$method($field, $input, $param);

        if (is_array($result)) :
            $this->errors[] = $result;
        endif;
    }

    
   /**
    * --------------------------------------------------------------------------
    * Verifica e valida o(s) método(s), caso ele(s) exista(m).
    * --------------------------------------------------------------------------
    * 
    * @param type $field
    * @param type $input
    * @param type $param
    */ 
    
    private function useValidation_methods($field, $input, $param) {
        $result = call_user_func(self::$validation_methods[$rule], $field, $input, $param);

            if($result === FALSE):
              $this->errors[] = array(
                'field' => $field,
                'value' => $input,
                'rule'  => self::$validation_methods[$rule],
                'param' => $param,
              );
            endif;

    }

    
    /**
     * -------------------------------------------------------------------------
     * Separa as regras em duas partes, sendo a segunda os parâmetros
     * -------------------------------------------------------------------------
     *  
     * @param type $rules
     * @param type $field
     * @param type $input
     * @throws Exception
     */
    
    private function rumRules($rules, $field, $input) {
        foreach ($rules as $rule):

            $method = NULL;
            $param  = NULL;

            // Verifica se temos parâmetros da regra
            if (strstr($rule, ':') !== FALSE):
                $rule_exp = explode(':', $rule);
                $method   = 'is_' . $rule_exp[0];
                $param    = $rule_exp[1];
                $rule     = $rule_exp[0];
            else:
                $method = 'is_' . $rule;
            endif;


            if (is_callable(array($this, $method))):
                $this->callFunc($method, $field, $input, $param);
            elseif(isset(self::$validation_methods[$rule])):
                $this->useValidation_methods($field, $input, $param);
            else:
                throw new Exception("Validator method '$method' does not exist.");
            endif;
        endforeach;
    }

    
    /**
     * -------------------------------------------------------------------------
     * Processar os erros de validação e retornar um array de erros com nomes de 
     * campo como chaves.
     * -------------------------------------------------------------------------
     * 
     * @param type $convert_to_string
     * 
     * @return type
     */
    
    private function fails($convert_to_string = NULL) {
        if (empty($this->errors)):
            return ($convert_to_string) ? NULL : array();
        endif;

        $message = array();

        foreach ($this->errors as $erro):
            $field = ucwords(str_replace(array('_', '-'), chr(32), $erro['field']));
            $param = $erro['param'];

            // Busca por nomes de campo explícitas, se eles existirem.
            if (array_key_exists($erro['field'], self::$fields)):
                $field = self::$fields[$erro['field']];
            endif;

            switch ($erro['rule']) {
                case 'mismatch' :
                    $message[$field] = "Não existe uma regra de validação para $field";
                    break;
                case 'is_required':
                    $message[$field] = "O campo $field é obrigatório";
                    break;
                case 'is_required_file':
                    $message[$field] = "O campo $field do tipo arquivo é obrigatório";
                    break;
                case 'is_extension':
                    $message[$field] = "O campo $field deve conter somente as extensões: $param";
                    break;
                case 'is_min_len':
                    $message[$field] = "O campo $field precisa conter no mínimo $param caracteres";
                    break;
                case 'is_max_len':
                    $message[$field] = "O campo $field precisa conter no máximo $param caracteres";
                    break;
                case 'is_min_value':
                    $message[$field] = "O campo $field precisa ser um valor numérico, igual ou superior a $param";
                    break;
                case 'is_max_value':
                    $message[$field] = "O campo $field precisa ser um valor numérico, igual ou inferior à $param";
                    break;
                case 'is_email':
                    $message[$field] = "O campo $field é necessário que seja um email válido";
                    break;
                case 'is_url':
                    $message[$field] = "O campo $field é necessário que seja uma URL válido";
                    break;
                case 'is_numeric':
                    $message[$field] = "O campo $field só pode conter caracteres numéricos";
                    break;
                case 'is_integer':
                    $message[$field] = "O campo $field só pode conter valor numérico";
                    break;
                case 'is_float':
                    $message[$field] = "O campo $field só pode conter valor flutuante(valor real)";
                    break;
                case 'is_string':
                    $message[$field] = "O campo $field tem que ser uma string válida";
                    break;
                case 'is_boolean':
                    $message[$field] = "O campo $field só pode conter um valor, verdadeiro ou falso";
                    break;
                case 'is_equals':
                    $message[$field] = "O valor do campo $field deve ser igual ao campo $param";
                    break;
                case 'is_not_equals':
                    $message[$field] = "O valor do campo $field não deve ser igual ao campo $param";
                    break;
                case 'is_date':
                    $message[$field] = "O campo $field precisa ser uma data válida";
                    break;
                case 'is_alpha':
                    $message[$field] = "O campo $field só pode conter caracteres alfa (a-z)";
                    break;
                case 'is_alpha_num':
                    $message[$field] = "O campo $field só pode conter caracteres alfanuméricos (a-z0-9)";
                    break;
                case 'is_phone':
                    $message[$field] = "O campo $field precisa ser um telefone válido";
                    break;
                case 'is_ip':
                    $message[$field] = "O campo $field precisa ser um endereço de IP válido";
                    break;
                case 'is_ipv4':
                    $message[$field] = "O campo $field precisa ser um endereço IPV4 válido";
                    break;
                case 'is_ipv6':
                    $message[$field] = "O campo $field precisa ser um endereço IPV6 válido";
                    break;
                case 'is_zip_code':
                    $message[$field] = "O campo $field precisa ser um CEP válido";
                    break;
                case 'is_plate':
                    $message[$field] = "O campo $field precisa ser uma placa de carro válida";
                    break;
                default:
                    $message[$field] = "O campo $field é inválido";
            }
        endforeach;

        return $message;
    }


    /** ------------------------- VALIDAÇÕES ---------------------------------- **/
     
    
    
    /**
     * -------------------------------------------------------------------------
     * Verifique se a chave especificada está presente e não está vazio.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'required'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_required($field, $input, $param = NULL) {
        if (isset($input[$field]) && ($input[$field] === FALSE || $input[$field] === 0 || $input[$field] === 0.0 || $input[$field] === '0' || !empty($input[$field]))):
            return;
        endif;

        return array(
            'field' => $field,
            'value' => NULL,
            'rule'  => __FUNCTION__,
            'param' => $param,
        );
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Verifica se um arquivo foi enviado.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'required_file'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_required_file($field, $input, $param = NULL){
        if ($input[$field]['error'] !== 4):
            return;
        endif;

        return array(
            'field' => $field,
            'value' => $input[$field],
            'rule'  => __FUNCTION__,
            'param' => $param,
        );
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Verifique o arquivo enviado para a extensão
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'extension:Z'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_extension($field, $input, $param = NULL) {
        if ($input[$field]['error'] !== 4):
            $param = trim(strtolower($param));
            $allowed_extensions = explode(', ', $param);
            
            if(!empty($input[$field])):
                $path_info = pathinfo($input[$field]['name']);
                $extension = $path_info['extension'];

                self::verifyExtension($extension, $allowed_extensions);
                
                return array(
                    'field' => $field,
                    'value' => $input[$field],
                    'rule'  => __FUNCTION__,
                    'param' => $param,
                );
            else:
                return;
            endif;
            
        endif;
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Verifica as extensões permitidas
     * -------------------------------------------------------------------------
     *  
     * @param type $extension
     * @param type $allowed_extensions
     * 
     * @return type
     */
    
    private static function verifyExtension($extension, $allowed_extensions) {
        if (in_array($extension, $allowed_extensions)):
            return;
        endif;
    }


    /**
     * -------------------------------------------------------------------------
     * Determina se o comprimento do valor fornecido é maior ou igual a um valor 
     * específico.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'min_len:4'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_min_len($field, $input, $param = NULL) {
        if (!isset($input[$field])):
            return;
        endif;

        
        if (function_exists('mb_strlen')):
            if (mb_strlen($input[$field]) >= (int) $param):
                return;
            endif;
        else:
            if (strlen($input[$field]) >= (int) $param):
                return;
            endif;
        endif;
        

        return array(
            'field' => $field,
            'value' => $input[$field],
            'rule'  => __FUNCTION__,
            'param' => $param,
        );
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Determina se o comprimento do valor fornecido é menor ou igual a um valor 
     * específico.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'max_len:200'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_max_len($field, $input, $param = NULL) {
        if (!isset($input[$field])):
            return;
        endif;

        if (function_exists('mb_strlen')):
            if (mb_strlen($input[$field]) <= (int) $param):
                return;
            endif;
        else:
            if (strlen($input[$field]) <= (int) $param):
                return;
            endif;
        endif;
        

        return array(
            'field' => $field,
            'value' => $input[$field],
            'rule'  => __FUNCTION__,
            'param' => $param,
        );
    }
    

    /**
     * -------------------------------------------------------------------------
     * Determinar se o valor numérico fornecido é maior ou igual a um valor 
     * específico.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'min_value:10'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_min_value($field, $input, $param = NULL) {
        if (!isset($input[$field])):
            return;
        endif;

        if (is_numeric($input[$field]) && is_numeric($param) && ($input[$field] >= $param)):
            return;
        endif;

        return array(
            'field' => $field,
            'value' => $input[$field],
            'rule'  => __FUNCTION__,
            'param' => $param,
        );
    }
    
 
    /**
     * -------------------------------------------------------------------------
     * Determinar se o valor numérico fornecido é menor ou igual a um valor 
     * específico.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'max_value:50'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_max_value($field, $input, $param = NULL) {
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        if (is_numeric($input[$field]) && is_numeric($param) && ($input[$field] <= $param)):
            return;
        endif;

        return array(
            'field' => $field,
            'value' => $input[$field],
            'rule'  => __FUNCTION__,
            'param' => $param,
        );
    }
    

    /**
     * -------------------------------------------------------------------------
     * Determina se o e-mail fornecido é válido.
     * -------------------------------------------------------------------------
     *  
     * Uso: '<index>' => 'email'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_email($field, $input, $param = NULL) {
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        if (!filter_var($input[$field], FILTER_VALIDATE_EMAIL)):
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Determina se o valor fornecido é uma URL válida.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'url'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_url($field, $input, $param = NULL) {
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        if (!filter_var($input[$field], FILTER_VALIDATE_URL)):
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Determina se o valor fornecido é um número válido ou sequência numérica.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'numeric'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_numeric($field, $input, $param = NULL) {
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        if (!is_numeric($input[$field])):
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }


    /**
     * -------------------------------------------------------------------------
     * Determina se o valor fornecido é um número inteiro válido.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'integer'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_integer($field, $input, $param = NULL){
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        if (filter_var($input[$field], FILTER_VALIDATE_INT) === FALSE):
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Determina se o valor fornecido é um flutuador(número real) válido.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'float'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_float($field, $input, $param = NULL) {
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        if (filter_var($input[$field], FILTER_VALIDATE_FLOAT) === FALSE):
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Determina se o valor fornecido é uma string válido.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'string'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_string($field, $input, $param = NULL) {
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        if (!is_string($input[$field])):
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }

    
    /**
     * -------------------------------------------------------------------------
     * Determinar se o valor fornecido é um PHP que aceite boolean.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'boolean'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * @return type
     */
    
    protected function is_boolean($field, $input, $param = NULL) {
        if (!isset($input[$field]) || empty($input[$field]) && $input[$field] !== 0):
            return;
        endif;

        if($input[$field] === TRUE || $input[$field] === FALSE):
          return;
        endif;

        return array(
            'field' => $field,
            'value' => $input[$field],
            'rule'  => __FUNCTION__,
            'param' => $param,
        );
    }


    /**
     * -------------------------------------------------------------------------
     * Determinar se o valor do campo fornecido é igual ao valor do campo atual.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'equals:C'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_equals($field, $input, $param = NULL) {
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        if ($input[$field] == $input[$param]):
          return;
        endif;

        return array(
            'field' => $field,
            'value' => $input[$field],
            'rule'  => __FUNCTION__,
            'param' => $param,
        );
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Determinar se o valor do campo fornecido é diferente ao valor do 
     * campo atual.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'not_equals:C'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_not_equals($field, $input, $param = NULL) {
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        if ($input[$field] != $input[$param]):
          return;
        endif;

        return array(
            'field' => $field,
            'value' => $input[$field],
            'rule'  => __FUNCTION__,
            'param' => $param,
        );
    }

    
    /**
     * -------------------------------------------------------------------------
     * Determina se a entrada fornecida é uma data válida (ISO 8601).
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'date'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_date($field, $input, $param = NULL) {
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        $date_1 = date('Y-m-d', strtotime($input[$field]));
        $date_2 = date('Y-m-d H:i:s', strtotime($input[$field]));

        if ($date_1 != $input[$field] && $date_2 != $input[$field]):
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }
  
   
    /**
     * -------------------------------------------------------------------------
     * Determina se o valor fornecido contém apenas caracteres alfabéticos.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'alpha'
     *  
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_alpha($field, $input, $param = NULL) {
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        if (!preg_match('/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/i', $input[$field]) !== FALSE):
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }
    
     
    /**
     * -------------------------------------------------------------------------
     * Determina se o valor fornecido contém apenas caracteres alfanuméricos.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'alpha_num'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_alpha_num($field, $input, $param = NULL){
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        if (!preg_match('/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖßÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i', $input[$field]) !== FALSE):
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Determina se o valor fornecido é um número de telefone válido.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'phone'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_phone($field, $input, $param = NULL)  {
        if (!isset($input[$field]) || empty($input[$field])) {
            return;
        }

        if (!preg_match('/^(\(0?\d{2}\)\s?|0?\d{2}[\s.-]?)\d{4,5}[\s.-]?\d{4}$/', $input[$field])) :
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }

    
    /**
     * -------------------------------------------------------------------------
     * Determinar se o valor fornecido é um endereço de IP válido.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'ip'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_ip($field, $input, $param = NULL) {
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        if (!filter_var($input[$field], FILTER_VALIDATE_IP) !== FALSE):
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Determinar se o valor fornecido é um endereço de IPV4 válido.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'ipv4'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_ipv4($field, $input, $param = NULL) {
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        if (!filter_var($input[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)):
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }

    
    /**
     * -------------------------------------------------------------------------
     * Determinar se o valor fornecido é um endereço de IPV6 válido.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'ipv6'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_ipv6($field, $input, $param = NULL) {
        if (!isset($input[$field]) || empty($input[$field])):
            return;
        endif;

        if (!filter_var($input[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)):
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }
    
    
    /**
     * -------------------------------------------------------------------------
     * Determina se o valor fornecido é um número de CEP válido.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'zip_code'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_zip_code($field, $input, $param = NULL)  {
        if (!isset($input[$field]) || empty($input[$field])) {
            return;
        }

        if (!preg_match('/^[0-9]{5}-[0-9]{3}$/', $input[$field])) :
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }
  
    
    /**
     * -------------------------------------------------------------------------
     * Determina se o valor fornecido é um número de Placa de Carro válido.
     * -------------------------------------------------------------------------
     * 
     * Uso: '<index>' => 'plate'
     * 
     * @param type $field
     * @param type $input
     * @param type $param
     * 
     * @return type
     */
    
    protected function is_plate($field, $input, $param = NULL)  {
        if (!isset($input[$field]) || empty($input[$field])) {
            return;
        }

        if (!preg_match('/^[A-Z]{3}\-[0-9]{4}$/', $input[$field])) :
            return array(
                'field' => $field,
                'value' => $input[$field],
                'rule'  => __FUNCTION__,
                'param' => $param,
            );
        endif;
    }
      
} 

