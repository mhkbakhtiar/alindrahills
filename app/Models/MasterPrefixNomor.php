<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MasterPrefixNomor extends Model
{
    protected $table = 'master_prefix_nomor';

    protected $fillable = [
        'kode_jenis',
        'nama_jenis',
        'prefix',
        'format_tahun',
        'format_bulan',
        'separator',
        'panjang_urutan',
        'reset_per',
        'nomor_terakhir',
        'contoh_hasil',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'nomor_terakhir' => 'integer',
        'panjang_urutan' => 'integer',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Generate Nomor ────────────────────────────────────────────────────────
    /**
     * Generate nomor berikutnya berdasarkan konfigurasi prefix ini.
     * Jika perlu reset (bulan/tahun baru), reset otomatis.
     */
    public function generateNomor(): string
    {
        $now    = Carbon::now();
        $sep    = $this->separator;
        $urutan = $this->nomor_terakhir + 1;

        // Susun segmen nomor
        $segments = [$this->prefix];

        if ($this->format_tahun !== 'none') {
            $segments[] = $this->format_tahun === 'YY'
                ? $now->format('y')
                : $now->format('Y');
        }

        if ($this->format_bulan !== 'none') {
            $segments[] = $now->format('m');
        }

        $segments[] = str_pad($urutan, $this->panjang_urutan, '0', STR_PAD_LEFT);

        return implode($sep, $segments);
    }

    /**
     * Generate dan simpan increment nomor_terakhir.
     * Panggil ini saat benar-benar memakai nomor (bukan hanya preview).
     */
    public function useNomor(): string
    {
        $nomor = $this->generateNomor();
        $this->increment('nomor_terakhir');
        return $nomor;
    }

    /**
     * Generate contoh hasil berdasarkan konfigurasi saat ini.
     */
    public function buildContoh(): string
    {
        $now    = Carbon::now();
        $sep    = $this->separator;

        $segments = [$this->prefix];

        if ($this->format_tahun !== 'none') {
            $segments[] = $this->format_tahun === 'YY'
                ? $now->format('y')
                : $now->format('Y');
        }

        if ($this->format_bulan !== 'none') {
            $segments[] = $now->format('m');
        }

        $segments[] = str_pad(1, $this->panjang_urutan, '0', STR_PAD_LEFT);

        return implode($sep, $segments);
    }

    /**
     * Static helper: ambil prefix berdasarkan kode_jenis dan generate nomor.
     */
    public static function generateFor(string $kodeJenis): string
    {
        $prefix = static::where('kode_jenis', $kodeJenis)->active()->firstOrFail();
        return $prefix->useNomor();
    }

    /**
     * Static helper: preview nomor berikutnya tanpa increment.
     */
    public static function previewFor(string $kodeJenis): string
    {
        $prefix = static::where('kode_jenis', $kodeJenis)->active()->first();
        return $prefix ? $prefix->generateNomor() : '-';
    }
}