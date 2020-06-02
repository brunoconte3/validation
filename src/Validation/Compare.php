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

    public static function differenceBetweenHours(string $hourIni, string $hourFin): string
    {
        $i = 1;
        $timeTotal = null;
        $times = [$hourFin, $hourIni];

        foreach ($times as $time) {
            $seconds = 0;
            list($h, $m, $s) = explode(':', $time);

            $seconds += $h * 3600;
            $seconds += $m * 60;
            $seconds += $s;

            $timeTotal[$i] = $seconds;
            $i++;
        }
        $seconds = $timeTotal[1] - $timeTotal[2];
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = str_pad((floor($seconds / 60)), 2, '0', STR_PAD_LEFT);
        $seconds -= $minutes * 60;

        if (substr($hours, 0, 1) == '-') {
            $hours = '-' . str_pad(substr($hours, 1, 2), 2, '0', STR_PAD_LEFT);
        } else {
            $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        }
        return "$hours:$minutes:$seconds";
    }
}
