<?php

namespace brunoconte3\Validation;

class Validator extends Rules
{
    public function set(array $dates, array $rules)
    {
        //prepara dados para validação
        $dates = json_decode($this->levelSubLevelsArrayReturnJson($dates), true);
        if (empty($dates)) {
            $this->errors['erro'] = "informe os dados!";
            return false;
        }
        //se for uma lista, valida a lista de objetos
        if (
            count(array_filter(array_keys($dates), 'is_numeric')) == count($dates)
            &&
            count(array_filter(array_values($dates), 'is_array')) == count($dates)
        ) {
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
}
