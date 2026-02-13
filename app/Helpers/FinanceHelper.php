<?php

use Illuminate\Database\Eloquent\Model;

if (! function_exists('total_biaya')) {
    function total_biaya(Model $model): float
    {
        return $model->itemJurnal()
            ->whereHas('perkiraan', function ($q) {
                $q->where('is_hpp', true);
            })
            ->sum('debet');
    }
}

if (! function_exists('sisa_pembayaran')) {
    function sisa_pembayaran(float $hargaJual, float $totalDibayar): float
    {
        return $hargaJual - $totalDibayar;
    }
}

if (! function_exists('persentase_pembayaran')) {
    function persentase_pembayaran(float $hargaJual, float $totalDibayar): float
    {
        if ($hargaJual <= 0) {
            return 0;
        }

        return ($totalDibayar / $hargaJual) * 100;
    }
}
