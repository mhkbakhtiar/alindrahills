<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Perkiraan;
use App\Models\ItemJurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PerkiraanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Perkiraan::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_perkiraan', 'like', "%{$search}%")
                  ->orWhere('nama_perkiraan', 'like', "%{$search}%");
            });
        }

        // Jenis akun filter
        if ($request->filled('jenis')) {
            $query->where('jenis_akun', $request->jenis);
        }

        // Order by kode_perkiraan
        $query->orderBy('kode_perkiraan');

        $perkiraan = $query->paginate(50)->withQueryString();

        return view('accounting.perkiraan.index', compact('perkiraan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Jenis akun options
        $jenis_akun = ['Aset', 'Kewajiban', 'Modal', 'Pendapatan', 'Biaya'];
        
        // Kategori options (sesuaikan dengan kebutuhan)
        $kategori = [
            'Kas & Bank',
            'Piutang',
            'Persediaan',
            'Aset Tetap',
            'Hutang Jangka Pendek',
            'Hutang Jangka Panjang',
            'Modal Saham',
            'Laba Ditahan',
            'Pendapatan Usaha',
            'Pendapatan Lain-lain',
            'Biaya Operasional',
            'Biaya Administrasi',
            'Biaya Penjualan',
        ];

        // Get header accounts for parent selection
        $parents = Perkiraan::where('is_header', true)
            ->orderBy('kode_perkiraan')
            ->get();

        return view('accounting.perkiraan.create', compact('jenis_akun', 'kategori', 'parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_perkiraan' => 'required|string|max:20|unique:perkiraan,kode_perkiraan',
            'nama_perkiraan' => 'required|string|max:255',
            'jenis_akun' => 'required|in:Aset,Kewajiban,Modal,Pendapatan,Biaya',
            'kategori' => 'nullable|string|max:100',
            'departemen' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:perkiraan,id',
            'anggaran' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'is_header' => 'nullable|boolean',
            'is_cash_bank' => 'nullable|boolean',
        ]);

        // Set defaults
        $validated['is_header'] = $request->has('is_header') ? true : false;
        $validated['is_cash_bank'] = $request->has('is_cash_bank') ? true : false;
        $validated['is_active'] = true;
        $validated['saldo_debet'] = 0;
        $validated['saldo_kredit'] = 0;

        Perkiraan::create($validated);

        return redirect()
            ->route('accounting.perkiraan.index')
            ->with('success', 'Perkiraan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Perkiraan $perkiraan)
    {
        // Load last 20 transactions with jurnal relation
        $perkiraan->load([
            'itemJurnal' => function($query) {
                $query->with('jurnal')
                    ->orderBy('created_at', 'desc')
                    ->limit(20);
            }
        ]);

        return view('accounting.perkiraan.show', compact('perkiraan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Perkiraan $perkiraan)
    {
        $jenis_akun = ['Aset', 'Kewajiban', 'Modal', 'Pendapatan', 'Biaya'];
        
        $kategori = [
            'Kas & Bank',
            'Piutang',
            'Persediaan',
            'Aset Tetap',
            'Hutang Jangka Pendek',
            'Hutang Jangka Panjang',
            'Modal Saham',
            'Laba Ditahan',
            'Pendapatan Usaha',
            'Pendapatan Lain-lain',
            'Biaya Operasional',
            'Biaya Administrasi',
            'Biaya Penjualan',
        ];

        // Get header accounts excluding current perkiraan
        $parents = Perkiraan::where('is_header', true)
            ->where('id', '!=', $perkiraan->id)
            ->orderBy('kode_perkiraan')
            ->get();

        return view('accounting.perkiraan.edit', compact('perkiraan', 'jenis_akun', 'kategori', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Perkiraan $perkiraan)
    {
        $validated = $request->validate([
            'kode_perkiraan' => [
                'required',
                'string',
                'max:20',
                Rule::unique('perkiraan', 'kode_perkiraan')->ignore($perkiraan->id)
            ],
            'nama_perkiraan' => 'required|string|max:255',
            'jenis_akun' => 'required|in:Aset,Kewajiban,Modal,Pendapatan,Biaya',
            'kategori' => 'nullable|string|max:100',
            'departemen' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:perkiraan,id',
            'anggaran' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'is_header' => 'nullable|boolean',
            'is_cash_bank' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_header'] = $request->has('is_header') ? true : false;
        $validated['is_cash_bank'] = $request->has('is_cash_bank') ? true : false;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $perkiraan->update($validated);

        return redirect()
            ->route('accounting.perkiraan.show', $perkiraan)
            ->with('success', 'Perkiraan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Perkiraan $perkiraan)
    {
        // Check if perkiraan has transactions
        if ($perkiraan->itemJurnal()->exists()) {
            return back()->with('error', 'Perkiraan tidak dapat dihapus karena sudah memiliki transaksi');
        }

        // Check if perkiraan has children
        if (Perkiraan::where('parent_id', $perkiraan->id)->exists()) {
            return back()->with('error', 'Perkiraan tidak dapat dihapus karena masih memiliki sub-perkiraan');
        }

        $perkiraan->delete();

        return redirect()
            ->route('accounting.perkiraan.index')
            ->with('success', 'Perkiraan berhasil dihapus');
    }

    /**
     * Export chart of accounts to Excel
     */
    public function export(Request $request)
    {
        $query = Perkiraan::query();

        if ($request->filled('jenis')) {
            $query->where('jenis_akun', $request->jenis);
        }

        $perkiraan = $query->orderBy('kode_perkiraan')->get();

        // You can use Laravel Excel or simple CSV export
        // For now, returning CSV export
        
        $filename = 'chart-of-accounts-' . date('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($perkiraan) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'Kode Perkiraan',
                'Nama Perkiraan',
                'Jenis Akun',
                'Kategori',
                'Departemen',
                'Saldo Debet',
                'Saldo Kredit',
                'Saldo',
                'Anggaran',
                'Status',
                'Is Header',
                'Is Cash/Bank'
            ]);

            // Data
            foreach ($perkiraan as $p) {
                fputcsv($file, [
                    $p->kode_perkiraan,
                    $p->nama_perkiraan,
                    $p->jenis_akun,
                    $p->kategori,
                    $p->departemen,
                    $p->saldo_debet,
                    $p->saldo_kredit,
                    $p->saldo,
                    $p->anggaran,
                    $p->is_active ? 'Aktif' : 'Nonaktif',
                    $p->is_header ? 'Ya' : 'Tidak',
                    $p->is_cash_bank ? 'Ya' : 'Tidak',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display ledger report for specific account
     */
    public function ledger(Request $request, Perkiraan $perkiraan)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        // Get transactions with running balance
        $items = ItemJurnal::with('jurnal')
            ->where('kode_perkiraan', $perkiraan->kode_perkiraan)
            ->whereHas('jurnal', function($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate])
                    ->where('status', 'posted');
            })
            ->orderBy('created_at')
            ->get();

        // Calculate running balance
        $runningBalance = 0;
        $items->transform(function($item) use (&$runningBalance) {
            $runningBalance += ($item->debet - $item->kredit);
            $item->running_balance = $runningBalance;
            return $item;
        });

        return view('accounting.perkiraan.ledger', compact('perkiraan', 'items', 'startDate', 'endDate'));
    }

    /**
     * Display trial balance report
     */
    public function trialBalance(Request $request)
    {
        $date = $request->input('date', now());

        $perkiraan = Perkiraan::where('is_active', true)
            ->where('is_header', false)
            ->orderBy('kode_perkiraan')
            ->get();

        // Calculate totals
        $totalDebet = $perkiraan->sum('saldo_debet');
        $totalKredit = $perkiraan->sum('saldo_kredit');

        return view('accounting.perkiraan.trial-balance', compact('perkiraan', 'totalDebet', 'totalKredit', 'date'));
    }

    /**
     * Display balance sheet
     */
    public function balanceSheet(Request $request)
    {
        $date = $request->input('date', now());

        // Assets
        $aset = Perkiraan::where('jenis_akun', 'Aset')
            ->where('is_active', true)
            ->orderBy('kode_perkiraan')
            ->get();

        // Liabilities
        $kewajiban = Perkiraan::where('jenis_akun', 'Kewajiban')
            ->where('is_active', true)
            ->orderBy('kode_perkiraan')
            ->get();

        // Equity
        $modal = Perkiraan::where('jenis_akun', 'Modal')
            ->where('is_active', true)
            ->orderBy('kode_perkiraan')
            ->get();

        $totalAset = $aset->sum('saldo');
        $totalKewajiban = $kewajiban->sum('saldo');
        $totalModal = $modal->sum('saldo');

        return view('accounting.perkiraan.balance-sheet', compact(
            'aset', 'kewajiban', 'modal',
            'totalAset', 'totalKewajiban', 'totalModal',
            'date'
        ));
    }

    /**
     * Display income statement
     */
    public function incomeStatement(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        // Revenue
        $pendapatan = Perkiraan::where('jenis_akun', 'Pendapatan')
            ->where('is_active', true)
            ->orderBy('kode_perkiraan')
            ->get();

        // Expenses
        $biaya = Perkiraan::where('jenis_akun', 'Biaya')
            ->where('is_active', true)
            ->orderBy('kode_perkiraan')
            ->get();

        $totalPendapatan = $pendapatan->sum('saldo_kredit');
        $totalBiaya = $biaya->sum('saldo_debet');
        $labaRugi = $totalPendapatan - $totalBiaya;

        return view('accounting.perkiraan.income-statement', compact(
            'pendapatan', 'biaya',
            'totalPendapatan', 'totalBiaya', 'labaRugi',
            'startDate', 'endDate'
        ));
    }

    /**
     * Recalculate balance for all accounts
     */
    public function recalculateBalances()
    {
        DB::beginTransaction();
        
        try {
            $perkiraan = Perkiraan::all();

            foreach ($perkiraan as $p) {
                $totalDebet = ItemJurnal::where('perkiraan_id', $p->id)
                    ->whereHas('jurnal', function($query) {
                        $query->where('status', 'posted');
                    })
                    ->sum('debet');

                $totalKredit = ItemJurnal::where('perkiraan_id', $p->id)
                    ->whereHas('jurnal', function($query) {
                        $query->where('status', 'posted');
                    })
                    ->sum('kredit');

                $p->update([
                    'saldo_debet' => $totalDebet,
                    'saldo_kredit' => $totalKredit,
                ]);
            }

            DB::commit();

            return back()->with('success', 'Saldo berhasil dihitung ulang untuk semua perkiraan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghitung ulang saldo: ' . $e->getMessage());
        }
    }
}