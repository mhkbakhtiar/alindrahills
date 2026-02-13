<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\ItemJurnal;
use App\Models\Perkiraan;
use App\Models\ProjectLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // =============================================================================
    // 1. JURNAL UMUM (General Journal)
    // =============================================================================
    
    /**
     * Laporan Jurnal Umum
     */
    public function jurnalUmum(Request $request)
    {
        $dari = $request->dari ?? date('Y-m-01');
        $sampai = $request->sampai ?? date('Y-m-t');

        $jurnal = Jurnal::with(['items.perkiraan', 'items.kavling'])
            ->posted()
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal')
            ->orderBy('nomor_bukti')
            ->get();

        return view('accounting.laporan.jurnal-umum', compact('jurnal', 'dari', 'sampai'));
    }

    public function jurnalUmumPrint(Request $request)
    {
        $dari = $request->dari ?? date('Y-m-01');
        $sampai = $request->sampai ?? date('Y-m-t');

        $jurnal = Jurnal::with(['items.perkiraan', 'items.kavling'])
            ->posted()
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal')
            ->orderBy('nomor_bukti')
            ->get();

        return view('accounting.laporan.jurnal-umum-print', compact('jurnal', 'dari', 'sampai'));
    }

    public function jurnalUmumExcel(Request $request)
    {
        $dari = $request->dari ?? date('Y-m-01');
        $sampai = $request->sampai ?? date('Y-m-t');

        $jurnal = Jurnal::with(['items.perkiraan'])
            ->posted()
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal')
            ->get();

        $filename = 'jurnal_umum_' . date('YmdHis') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Header
        fputcsv($handle, ['Tanggal', 'No. Bukti', 'Kode Perkiraan', 'Nama Perkiraan', 'Keterangan', 'Debet', 'Kredit']);

        foreach ($jurnal as $j) {
            foreach ($j->items as $item) {
                fputcsv($handle, [
                    $j->tanggal->format('d/m/Y'),
                    $j->nomor_bukti,
                    $item->kode_perkiraan,
                    $item->perkiraan->nama_perkiraan,
                    $item->keterangan,
                    $item->debet,
                    $item->kredit,
                ]);
            }
        }

        fclose($handle);
        exit;
    }

    // =============================================================================
    // 2. BUKU BESAR (General Ledger)
    // =============================================================================
    
    /**
     * Laporan Buku Besar
     */
    public function bukuBesar(Request $request)
    {
        $dari = $request->dari ?? date('Y-m-01');
        $sampai = $request->sampai ?? date('Y-m-t');
        $kodePerkiraan = $request->kode_perkiraan;

        $perkiraan = Perkiraan::active()->detail()->orderBy('kode_perkiraan')->get();

        $data = [];

        if ($kodePerkiraan) {
            $akun = Perkiraan::where('kode_perkiraan', $kodePerkiraan)->first();
            
            if ($akun) {
                // Get saldo awal (before 'dari')
                $saldoAwal = $this->getSaldoAwal($kodePerkiraan, $dari);

                // Get mutasi
                $mutasi = ItemJurnal::with(['jurnal'])
                    ->where('kode_perkiraan', $kodePerkiraan)
                    ->whereHas('jurnal', function($q) use ($dari, $sampai) {
                        $q->posted()
                          ->whereBetween('tanggal', [$dari, $sampai]);
                    })
                    ->orderBy('id')
                    ->get();

                $data = [
                    'akun' => $akun,
                    'saldo_awal' => $saldoAwal,
                    'mutasi' => $mutasi,
                ];
            }
        }

        return view('accounting.laporan.buku-besar', compact('perkiraan', 'data', 'dari', 'sampai', 'kodePerkiraan'));
    }

    public function bukuBesarPrint(Request $request)
    {
        $dari = $request->dari ?? date('Y-m-01');
        $sampai = $request->sampai ?? date('Y-m-t');
        $kodePerkiraan = $request->kode_perkiraan;

        $akun = Perkiraan::where('kode_perkiraan', $kodePerkiraan)->first();
        $saldoAwal = $this->getSaldoAwal($kodePerkiraan, $dari);

        $mutasi = ItemJurnal::with(['jurnal'])
            ->where('kode_perkiraan', $kodePerkiraan)
            ->whereHas('jurnal', function($q) use ($dari, $sampai) {
                $q->posted()->whereBetween('tanggal', [$dari, $sampai]);
            })
            ->orderBy('id')
            ->get();

        return view('accounting.laporan.buku-besar-print', compact('akun', 'saldoAwal', 'mutasi', 'dari', 'sampai'));
    }

    public function bukuBesarExcel(Request $request)
    {
        $dari = $request->dari ?? date('Y-m-01');
        $sampai = $request->sampai ?? date('Y-m-t');
        $kodePerkiraan = $request->kode_perkiraan;

        $akun = Perkiraan::where('kode_perkiraan', $kodePerkiraan)->first();
        $saldoAwal = $this->getSaldoAwal($kodePerkiraan, $dari);

        $mutasi = ItemJurnal::with(['jurnal'])
            ->where('kode_perkiraan', $kodePerkiraan)
            ->whereHas('jurnal', function($q) use ($dari, $sampai) {
                $q->posted()->whereBetween('tanggal', [$dari, $sampai]);
            })
            ->orderBy('id')
            ->get();

        $filename = 'buku_besar_' . $kodePerkiraan . '_' . date('YmdHis') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Header
        fputcsv($handle, ['Tanggal', 'No. Bukti', 'Keterangan', 'Debet', 'Kredit', 'Saldo']);

        $saldo = $saldoAwal;
        fputcsv($handle, ['', '', 'Saldo Awal', '', '', $saldo]);

        foreach ($mutasi as $item) {
            $saldo += ($item->debet - $item->kredit);
            fputcsv($handle, [
                $item->jurnal->tanggal->format('d/m/Y'),
                $item->jurnal->nomor_bukti,
                $item->keterangan,
                $item->debet,
                $item->kredit,
                $saldo,
            ]);
        }

        fclose($handle);
        exit;
    }

    // =============================================================================
    // 3. BUKU PEMBANTU PER KAVLING (Subsidiary Ledger by Project)
    // =============================================================================
    
    /**
     * Laporan Buku Pembantu per Kavling
     */
    public function bukuPembantuKavling(Request $request)
    {
        $dari = $request->dari ?? date('Y-m-01');
        $sampai = $request->sampai ?? date('Y-m-t');
        $kodeKavling = $request->kode_kavling;

        $kavling = ProjectLocation::orderBy('kavling')->get();

        $data = [];

        if ($kodeKavling) {
            $kavlingData = ProjectLocation::with('owner')->where('kavling', $kodeKavling)->first();
            
            if ($kavlingData) {
                // Get all transactions for this kavling
                $transaksi = ItemJurnal::with(['jurnal', 'perkiraan'])
                    ->where('kode_kavling', $kodeKavling)
                    ->whereHas('jurnal', function($q) use ($dari, $sampai) {
                        $q->posted()->whereBetween('tanggal', [$dari, $sampai]);
                    })
                    ->orderBy('id')
                    ->get();

                // Group by perkiraan
                $grouped = $transaksi->groupBy('kode_perkiraan');

                // Calculate totals
                $totalDebet = $transaksi->sum('debet');
                $totalKredit = $transaksi->sum('kredit');

                // Get HPP (Harga Pokok) - biaya pembangunan
                $hpp = $transaksi->where(function($item) {
                    return $item->perkiraan->is_hpp ?? false;
                })->sum('debet');

                // Get pembayaran (uang muka, cicilan)
                $pembayaran = $transaksi->where('kode_perkiraan', 'LIKE', '211%')->sum('kredit');

                $data = [
                    'kavling' => $kavlingData,
                    'transaksi' => $transaksi,
                    'grouped' => $grouped,
                    'total_debet' => $totalDebet,
                    'total_kredit' => $totalKredit,
                    'hpp' => $hpp,
                    'pembayaran' => $pembayaran,
                    'sisa_pembayaran' => ($kavlingData->harga_jual ?? 0) - $pembayaran,
                ];
            }
        }

        return view('accounting.laporan.buku-pembantu-kavling', compact('kavling', 'data', 'dari', 'sampai', 'kodeKavling'));
    }

    public function bukuPembantuKavlingPrint(Request $request)
    {
        $dari = $request->dari ?? date('Y-m-01');
        $sampai = $request->sampai ?? date('Y-m-t');
        $kodeKavling = $request->kode_kavling;

        $kavlingData = ProjectLocation::with('owner')->where('kavling', $kodeKavling)->first();
        
        $transaksi = ItemJurnal::with(['jurnal', 'perkiraan'])
            ->where('kode_kavling', $kodeKavling)
            ->whereHas('jurnal', function($q) use ($dari, $sampai) {
                $q->posted()->whereBetween('tanggal', [$dari, $sampai]);
            })
            ->orderBy('id')
            ->get();

        $grouped = $transaksi->groupBy('kode_perkiraan');
        $totalDebet = $transaksi->sum('debet');
        $totalKredit = $transaksi->sum('kredit');

        return view('accounting.laporan.buku-pembantu-kavling-print', compact('kavlingData', 'transaksi', 'grouped', 'totalDebet', 'totalKredit', 'dari', 'sampai'));
    }

    // =============================================================================
    // 4. NERACA (Balance Sheet)
    // =============================================================================
    
    /**
     * Laporan Neraca
     */
    public function neraca(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-t');

        // Get all perkiraan with saldo
        $aset = $this->getNeracaData('Aset', $tanggal);
        $kewajiban = $this->getNeracaData('Kewajiban', $tanggal);
        $modal = $this->getNeracaData('Modal', $tanggal);

        // Calculate laba/rugi berjalan
        $labaRugi = $this->getLabaRugiBerjalan($tanggal);

        $totalAset = $aset->sum('saldo');
        $totalKewajiban = $kewajiban->sum('saldo');
        $totalModal = $modal->sum('saldo') + $labaRugi;

        return view('accounting.laporan.neraca', compact(
            'aset', 'kewajiban', 'modal', 'labaRugi',
            'totalAset', 'totalKewajiban', 'totalModal', 'tanggal'
        ));
    }

    public function neracaPrint(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-t');

        $aset = $this->getNeracaData('Aset', $tanggal);
        $kewajiban = $this->getNeracaData('Kewajiban', $tanggal);
        $modal = $this->getNeracaData('Modal', $tanggal);
        $labaRugi = $this->getLabaRugiBerjalan($tanggal);

        $totalAset = $aset->sum('saldo');
        $totalKewajiban = $kewajiban->sum('saldo');
        $totalModal = $modal->sum('saldo') + $labaRugi;

        return view('accounting.laporan.neraca-print', compact(
            'aset', 'kewajiban', 'modal', 'labaRugi',
            'totalAset', 'totalKewajiban', 'totalModal', 'tanggal'
        ));
    }

    public function neracaExcel(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-t');

        $aset = $this->getNeracaData('Aset', $tanggal);
        $kewajiban = $this->getNeracaData('Kewajiban', $tanggal);
        $modal = $this->getNeracaData('Modal', $tanggal);

        $filename = 'neraca_' . date('YmdHis') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['NERACA PER ' . date('d/m/Y', strtotime($tanggal))]);
        fputcsv($handle, []);

        fputcsv($handle, ['ASET']);
        foreach ($aset as $item) {
            fputcsv($handle, [$item->kode_perkiraan, $item->nama_perkiraan, $item->saldo]);
        }
        fputcsv($handle, ['TOTAL ASET', '', $aset->sum('saldo')]);
        fputcsv($handle, []);

        fputcsv($handle, ['KEWAJIBAN']);
        foreach ($kewajiban as $item) {
            fputcsv($handle, [$item->kode_perkiraan, $item->nama_perkiraan, $item->saldo]);
        }
        fputcsv($handle, ['TOTAL KEWAJIBAN', '', $kewajiban->sum('saldo')]);
        fputcsv($handle, []);

        fputcsv($handle, ['MODAL']);
        foreach ($modal as $item) {
            fputcsv($handle, [$item->kode_perkiraan, $item->nama_perkiraan, $item->saldo]);
        }
        fputcsv($handle, ['TOTAL MODAL', '', $modal->sum('saldo')]);

        fclose($handle);
        exit;
    }

    // =============================================================================
    // 5. LABA RUGI (Income Statement)
    // =============================================================================
    
    /**
     * Laporan Laba Rugi
     */
    public function labaRugi(Request $request)
    {
        $dari = $request->dari ?? date('Y-m-01');
        $sampai = $request->sampai ?? date('Y-m-t');

        $pendapatan = $this->getLabaRugiData('Pendapatan', $dari, $sampai);
        $biaya = $this->getLabaRugiData('Biaya', $dari, $sampai);

        $totalPendapatan = $pendapatan->sum('saldo');
        $totalBiaya = $biaya->sum('saldo');
        $labaRugi = $totalPendapatan - $totalBiaya;

        return view('accounting.laporan.laba-rugi', compact(
            'pendapatan', 'biaya', 'totalPendapatan', 'totalBiaya', 'labaRugi', 'dari', 'sampai'
        ));
    }

    public function labaRugiPrint(Request $request)
    {
        $dari = $request->dari ?? date('Y-m-01');
        $sampai = $request->sampai ?? date('Y-m-t');

        $pendapatan = $this->getLabaRugiData('Pendapatan', $dari, $sampai);
        $biaya = $this->getLabaRugiData('Biaya', $dari, $sampai);

        $totalPendapatan = $pendapatan->sum('saldo');
        $totalBiaya = $biaya->sum('saldo');
        $labaRugi = $totalPendapatan - $totalBiaya;

        return view('accounting.laporan.laba-rugi-print', compact(
            'pendapatan', 'biaya', 'totalPendapatan', 'totalBiaya', 'labaRugi', 'dari', 'sampai'
        ));
    }

    public function labaRugiExcel(Request $request)
    {
        $dari = $request->dari ?? date('Y-m-01');
        $sampai = $request->sampai ?? date('Y-m-t');

        $pendapatan = $this->getLabaRugiData('Pendapatan', $dari, $sampai);
        $biaya = $this->getLabaRugiData('Biaya', $dari, $sampai);

        $filename = 'laba_rugi_' . date('YmdHis') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['LAPORAN LABA RUGI']);
        fputcsv($handle, ['Periode: ' . date('d/m/Y', strtotime($dari)) . ' s/d ' . date('d/m/Y', strtotime($sampai))]);
        fputcsv($handle, []);

        fputcsv($handle, ['PENDAPATAN']);
        foreach ($pendapatan as $item) {
            fputcsv($handle, [$item->kode_perkiraan, $item->nama_perkiraan, $item->saldo]);
        }
        fputcsv($handle, ['TOTAL PENDAPATAN', '', $pendapatan->sum('saldo')]);
        fputcsv($handle, []);

        fputcsv($handle, ['BIAYA']);
        foreach ($biaya as $item) {
            fputcsv($handle, [$item->kode_perkiraan, $item->nama_perkiraan, $item->saldo]);
        }
        fputcsv($handle, ['TOTAL BIAYA', '', $biaya->sum('saldo')]);
        fputcsv($handle, []);

        $labaRugi = $pendapatan->sum('saldo') - $biaya->sum('saldo');
        fputcsv($handle, ['LABA/RUGI', '', $labaRugi]);

        fclose($handle);
        exit;
    }

    // =============================================================================
    // 6. CALK (Catatan Atas Laporan Keuangan)
    // =============================================================================
    
    /**
     * Laporan CALK
     */
    public function calk(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-t');

        // Detail breakdown per kategori
        $data = [
            'kas_bank' => $this->getCalkDetail('11', $tanggal, 'Kas dan Bank'),
            'piutang' => $this->getCalkDetail('115', $tanggal, 'Piutang Konsumen'),
            'persediaan_tanah' => $this->getCalkDetail('12', $tanggal, 'Persediaan Tanah'),
            'persediaan_bangunan' => $this->getCalkDetail('13', $tanggal, 'Persediaan Bangunan'),
            'aktiva_tetap' => $this->getCalkDetail('14', $tanggal, 'Aktiva Tetap'),
            'hutang' => $this->getCalkDetail('21', $tanggal, 'Hutang'),
            'modal' => $this->getCalkDetail('23', $tanggal, 'Modal'),
        ];

        return view('accounting.laporan.calk', compact('data', 'tanggal'));
    }

    public function calkPrint(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-t');

        $data = [
            'kas_bank' => $this->getCalkDetail('11', $tanggal, 'Kas dan Bank'),
            'piutang' => $this->getCalkDetail('115', $tanggal, 'Piutang Konsumen'),
            'persediaan_tanah' => $this->getCalkDetail('12', $tanggal, 'Persediaan Tanah'),
            'persediaan_bangunan' => $this->getCalkDetail('13', $tanggal, 'Persediaan Bangunan'),
            'aktiva_tetap' => $this->getCalkDetail('14', $tanggal, 'Aktiva Tetap'),
            'hutang' => $this->getCalkDetail('21', $tanggal, 'Hutang'),
            'modal' => $this->getCalkDetail('23', $tanggal, 'Modal'),
        ];

        return view('accounting.laporan.calk-print', compact('data', 'tanggal'));
    }

    // =============================================================================
    // HELPER METHODS
    // =============================================================================
    
    /**
     * Get saldo awal for buku besar
     */
    private function getSaldoAwal($kodePerkiraan, $tanggal)
    {
        $perkiraan = Perkiraan::where('kode_perkiraan', $kodePerkiraan)->first();
        
        if (!$perkiraan) return 0;

        // Get all transactions before tanggal
        $mutasi = ItemJurnal::where('kode_perkiraan', $kodePerkiraan)
            ->whereHas('jurnal', function($q) use ($tanggal) {
                $q->posted()->where('tanggal', '<', $tanggal);
            })
            ->get();

        $totalDebet = $mutasi->sum('debet');
        $totalKredit = $mutasi->sum('kredit');

        return $totalDebet - $totalKredit;
    }

    /**
     * Get data for neraca
     */
    private function getNeracaData($jenisAkun, $tanggal)
    {
        return Perkiraan::where('jenis_akun', $jenisAkun)
            ->active()
            ->get()
            ->map(function($p) use ($tanggal) {
                $saldo = $this->getSaldoSampai($p->kode_perkiraan, $tanggal);
                $p->saldo = $saldo;
                return $p;
            })
            ->filter(function($p) {
                return $p->saldo != 0;
            });
    }

    /**
     * Get laba rugi berjalan for neraca
     */
    private function getLabaRugiBerjalan($tanggal)
    {
        $pendapatan = $this->getLabaRugiData('Pendapatan', date('Y-01-01'), $tanggal);
        $biaya = $this->getLabaRugiData('Biaya', date('Y-01-01'), $tanggal);

        return $pendapatan->sum('saldo') - $biaya->sum('saldo');
    }

    /**
     * Get data for laba rugi
     */
    private function getLabaRugiData($jenisAkun, $dari, $sampai)
    {
        return Perkiraan::where('jenis_akun', $jenisAkun)
            ->active()
            ->get()
            ->map(function($p) use ($dari, $sampai) {
                $mutasi = ItemJurnal::where('kode_perkiraan', $p->kode_perkiraan)
                    ->whereHas('jurnal', function($q) use ($dari, $sampai) {
                        $q->posted()->whereBetween('tanggal', [$dari, $sampai]);
                    })
                    ->get();

                // Untuk pendapatan: kredit - debet
                // Untuk biaya: debet - kredit
                if ($p->jenis_akun == 'Pendapatan') {
                    $saldo = $mutasi->sum('kredit') - $mutasi->sum('debet');
                } else {
                    $saldo = $mutasi->sum('debet') - $mutasi->sum('kredit');
                }

                $p->saldo = $saldo;
                return $p;
            })
            ->filter(function($p) {
                return $p->saldo != 0;
            });
    }

    /**
     * Get saldo sampai tanggal tertentu
     */
    private function getSaldoSampai($kodePerkiraan, $tanggal)
    {
        $mutasi = ItemJurnal::where('kode_perkiraan', $kodePerkiraan)
            ->whereHas('jurnal', function($q) use ($tanggal) {
                $q->posted()->where('tanggal', '<=', $tanggal);
            })
            ->get();

        return $mutasi->sum('debet') - $mutasi->sum('kredit');
    }

    /**
     * Get detail for CALK
     */
    private function getCalkDetail($prefix, $tanggal, $title)
    {
        $perkiraan = Perkiraan::where('kode_perkiraan', 'LIKE', $prefix . '%')
            ->active()
            ->get()
            ->map(function($p) use ($tanggal) {
                $saldo = $this->getSaldoSampai($p->kode_perkiraan, $tanggal);
                $p->saldo = $saldo;
                return $p;
            })
            ->filter(function($p) {
                return $p->saldo != 0;
            });

        return [
            'title' => $title,
            'items' => $perkiraan,
            'total' => $perkiraan->sum('saldo'),
        ];
    }
}