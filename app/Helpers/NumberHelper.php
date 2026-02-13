<?php

namespace App\Helpers;

class NumberHelper
{
    public static function formatRupiah($number)
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }

    public static function formatDecimal($number, $decimals = 2)
    {
        return number_format($number, $decimals, ',', '.');
    }
}