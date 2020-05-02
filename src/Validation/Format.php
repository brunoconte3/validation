<?php

namespace brunoconte3\Validation;

class Format
{
    public static function formatCnpj(string $cnpj): string
    {
        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj);
    }

    public static function formatCpf(string $cpf): string
    {
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cpf);
    }

    public static function formatTelephone(int $number): string
    {
        $number = '(' . substr($number, 0, 2) . ') ' . substr($number, 2, -4) . '-' . substr($number, -4);
        return $number;
    }

    public static function formatCep(int $valor): string
    {
        return substr($valor, 0, 5) . '-' . substr($valor, 5, 3);
    }
}
