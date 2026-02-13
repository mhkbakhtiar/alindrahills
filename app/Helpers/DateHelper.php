<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function toIndo($date)
    {
        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $carbon = Carbon::parse($date);
        return $carbon->day . ' ' . $months[$carbon->month] . ' ' . $carbon->year;
    }
}