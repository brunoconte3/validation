<?php

namespace brunoconte3\Validation;

trait TraitRuleInteger
{
    protected function validateInteger($rule = '', $field = '', $value = null, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $this->errors[$field] = !empty($message) ?
                $message : "O campo $field deve ser do tipo inteiro!";
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
}
