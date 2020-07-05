<?php

namespace brunoconte3\Validation;

class Validator extends Rules
{
    public function set(array $data, array $rules)
    {
        //prepara dados para validação
        $data = json_decode($this->levelSubLevelsArrayReturnJson($data), true);
        if (empty($data)) {
            $this->errors['erro'] = 'informe os dados!';
            return false;
        }
        //se for uma lista, valida a lista de objetos
        if (
            count(array_filter(array_keys($data), 'is_numeric')) == count($data)
            &&
            count(array_filter(array_values($data), 'is_array')) == count($data)
        ) {
            foreach ($data as $val) {
                $this->validateSubLevelData($val, $rules, true);
            }
        } else {
            $this->validateSubLevelData($data, $rules, true);
        }
        return true;
    }

    public function getErros()
    {
        return $this->errors;
    }
}
