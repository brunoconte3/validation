<?php

namespace brunoconte3\Validation;

class ValidateDate
{
    public static function validateDateBrazil(string $data): bool
    {
        if (strlen($data) < 8) {
            return false;
        } else {
            if (strpos($data, '/') !== false) {
                $partes = explode('/', $data);
                $dia = $partes[0];
                $mes = $partes[1];
                $ano = isset($partes[2]) ? $partes[2] : 0;

                if (strlen($ano) < 4) {
                    return false;
                } else {
                    if (checkdate($mes, $dia, $ano)) {
                        return true;
                    }
                    return false;
                }
            }
            return false;
        }
    }
}
