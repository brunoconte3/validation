<?php

namespace brunoconte3\Validation;

use DateTime;

class Compare
{
    public static function daysDifferenceBetweenData(string $dtIni, string $dtFin): string
    {
        if (strpos($dtIni, '/') > -1) {
            $dtIni = implode('-', array_reverse(explode('/', $dtIni)));
        }
        if (strpos($dtFin, '/') > -1) {
            $dtFin = implode('-', array_reverse(explode('/', $dtFin)));
        }

        $datetime1 = new DateTime($dtIni);
        $datetime2 = new DateTime($dtFin);
        $interval = $datetime1->diff($datetime2);

        return $interval->format('%R%a');
    }

    public static function startDateLessThanEnd(
        string $dtIni,
        string $dtFin,
        string $msg = 'Data Inicial não pode ser maior que a Data Final!'
    ): ?string {

        if (!empty($dtIni) && !empty($dtFin)) {
            if (self::daysDifferenceBetweenData($dtIni, $dtFin) < 0) {
                return $msg;
            }
        } else {
            return 'Campos datas não foram preenchidas!';
        }
        return null;
    }
}
