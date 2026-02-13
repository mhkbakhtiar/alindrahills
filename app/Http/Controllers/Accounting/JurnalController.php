<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\ItemJurnal;
use App\Models\Perkiraan;
use App\Models\ProjectLocation;
use App\Models\User;
use App\Models\KavlingPembeli;
use App\Models\TahunAnggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JurnalController extends Controller
{
    /**
     * Display a listing of jurnal
     */
    public function index(Request $request)
    {
        $query = Jurnal::with(['creator', 'tahunAnggaran'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('nomor_bukti', 'desc');

        // Filter by date range
        if ($request->has('dari') && $request->dari != '') {
            $query->where('tanggal', '>=', $request->dari);
        }
        if ($request->has('sampai') && $request->sampai != '') {
            $query->where('tanggal', '<=', $request->sampai);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by nomor bukti
        if ($request->has('search') && $request->search != '') {
            $query->where('nomor_bukti', 'LIKE', "%{$request->search}%");
        }

        $jurnal = $query->paginate(20);

        return view('accounting.jurnal.index', compact('jurnal'));
    }

    /**
     * Show the form for creating new jurnal
     */
    public function create()
    {
        $tahunAnggaran = TahunAnggaran::active()->first();
        $perkiraan = Perkiraan::active()->details()->orderBy('kode_perkiraan')->get();
        
        // Get kavling pembeli yang aktif (tidak batal)
        $kavlingPembeli = KavlingPembeli::with(['kavling', 'pembeli'])
            ->where('status', '!=', 'batal')
            ->get();
        
        // Generate nomor bukti otomatis
        $nomorBukti = $this->generateNomorBukti();

        return view('accounting.jurnal.create', compact('tahunAnggaran', 'perkiraan', 'kavlingPembeli', 'nomorBukti'));
    }

    /**
     * Store a newly created jurnal with items
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_bukti' => 'required|unique:jurnal,nomor_bukti|max:50',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable',
            'jenis_jurnal' => 'required|in:umum,penyesuaian,penutup,pembalik',
            'departemen' => 'nullable|max:50',
            
            // Items array
            'items' => 'required|array|min:2',
            'items.*.kode_perkiraan' => 'required|exists:perkiraan,kode_perkiraan',
            'items.*.keterangan' => 'nullable|string',
            'items.*.debet' => 'required|numeric|min:0',
            'items.*.kredit' => 'required|numeric|min:0',
            'items.*.kode_kavling' => 'nullable|exists:project_locations,kavling',
            'items.*.id_user' => 'nullable|exists:users,user_id',
        ]);

        try {
            DB::beginTransaction();

            // Validate balance
            $totalDebet = collect($validated['items'])->sum('debet');
            $totalKredit = collect($validated['items'])->sum('kredit');

            if ($totalDebet != $totalKredit) {
                return back()
                    ->withInput()
                    ->with('error', "Jurnal tidak balance! Debet: {$totalDebet}, Kredit: {$totalKredit}");
            }

            // Validate each item must have either debet or kredit (not both)
            foreach ($validated['items'] as $item) {
                if ($item['debet'] > 0 && $item['kredit'] > 0) {
                    return back()
                        ->withInput()
                        ->with('error', 'Setiap item jurnal harus memiliki DEBET atau KREDIT saja, tidak boleh keduanya!');
                }
                if ($item['debet'] == 0 && $item['kredit'] == 0) {
                    return back()
                        ->withInput()
                        ->with('error', 'Setiap item jurnal harus memiliki nilai DEBET atau KREDIT!');
                }
            }

            // Get tahun anggaran aktif
            $tahunAnggaran = TahunAnggaran::active()->first();

            // Create jurnal
            $jurnal = Jurnal::create([
                'nomor_bukti' => $validated['nomor_bukti'],
                'tanggal' => $validated['tanggal'],
                'keterangan' => $validated['keterangan'],
                'jenis_jurnal' => $validated['jenis_jurnal'],
                'departemen' => $validated['departemen'],
                'id_tahun_anggaran' => $tahunAnggaran?->id,
                'created_by' => auth()->user()->user_id,
                'status' => 'draft',
            ]);

            // Create items
            foreach ($validated['items'] as $index => $item) {
                ItemJurnal::create([
                    'id_jurnal' => $jurnal->id,
                    'kode_perkiraan' => $item['kode_perkiraan'],
                    'keterangan' => $item['keterangan'],
                    'debet' => $item['debet'],
                    'kredit' => $item['kredit'],
                    'kode_kavling' => $item['kode_kavling'] ?? null,
                    'id_user' => $item['id_user'] ?? null,
                    'urutan' => $index + 1,
                ]);

                // Update saldo perkiraan
                $this->updateSaldoPerkiraan(
                    $item['kode_perkiraan'], 
                    $item['debet'], 
                    $item['kredit']
                );
            }

            DB::commit();

            return redirect()
                ->route('accounting.jurnal.show', $jurnal)
                ->with('success', 'Jurnal berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified jurnal
     */
    public function show(Jurnal $jurnal)
    {
        $jurnal->load([
            'items.perkiraan',
            'items.kavling',
            'items.user',
            'creator',
            'tahunAnggaran'
        ]);

        return view('accounting.jurnal.show', compact('jurnal'));
    }

    /**
     * Show the form for editing jurnal
     */
    public function edit(Jurnal $jurnal)
    {
        // Only draft can be edited
        if ($jurnal->status !== 'draft') {
            return back()->with('error', 'Hanya jurnal dengan status DRAFT yang dapat diedit');
        }

        $jurnal->load('items');
        $perkiraan = Perkiraan::active()->detail()->orderBy('kode_perkiraan')->get();
        $tahunAnggaran = TahunAnggaran::active()->first();

        return view('accounting.jurnal.edit', compact('jurnal', 'perkiraan', 'tahunAnggaran'));
    }

    /**
     * Update the specified jurnal
     */
    public function update(Request $request, Jurnal $jurnal)
    {
        if ($jurnal->status !== 'draft') {
            return back()->with('error', 'Hanya jurnal DRAFT yang dapat diupdate');
        }

        $validated = $request->validate([
            'nomor_bukti' => 'required|max:50|unique:jurnal,nomor_bukti,' . $jurnal->id,
            'tanggal' => 'required|date',
            'keterangan' => 'nullable',
            'items' => 'required|array|min:2',
            'items.*.kode_perkiraan' => 'required|exists:perkiraan,kode_perkiraan',
            'items.*.keterangan' => 'nullable|string',
            'items.*.debet' => 'required|numeric|min:0',
            'items.*.kredit' => 'required|numeric|min:0',
            'items.*.kode_kavling' => 'nullable|exists:project_locations,kavling',
            'items.*.id_user' => 'nullable|exists:users,user_id',
        ]);

        try {
            DB::beginTransaction();

            // Validate balance
            $totalDebet = collect($validated['items'])->sum('debet');
            $totalKredit = collect($validated['items'])->sum('kredit');

            if ($totalDebet != $totalKredit) {
                return back()->withInput()->with('error', 'Jurnal tidak balance!');
            }

            // Update jurnal
            $jurnal->update([
                'nomor_bukti' => $validated['nomor_bukti'],
                'tanggal' => $validated['tanggal'],
                'keterangan' => $validated['keterangan'],
                'updated_by' => auth()->user()->user_id,
            ]);

            // Delete old items and create new ones
            $jurnal->items()->delete();

            foreach ($validated['items'] as $index => $item) {
                ItemJurnal::create([
                    'id_jurnal' => $jurnal->id,
                    'kode_perkiraan' => $item['kode_perkiraan'],
                    'keterangan' => $item['keterangan'],
                    'debet' => $item['debet'],
                    'kredit' => $item['kredit'],
                    'kode_kavling' => $item['kode_kavling'] ?? null,
                    'id_user' => $item['id_user'] ?? null,
                    'urutan' => $index + 1,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('accounting.jurnal.show', $jurnal)
                ->with('success', 'Jurnal berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified jurnal
     */
    public function destroy(Jurnal $jurnal)
    {
        if ($jurnal->status !== 'draft') {
            return back()->with('error', 'Hanya jurnal DRAFT yang dapat dihapus');
        }

        try {
            DB::beginTransaction();

            // Delete items first
            $jurnal->items()->delete();
            
            // Delete jurnal
            $jurnal->delete();

            DB::commit();

            return redirect()
                ->route('accounting.jurnal.index')
                ->with('success', 'Jurnal berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Post jurnal (change status from draft to posted)
     */
    public function post(Jurnal $jurnal)
    {
        if ($jurnal->status !== 'draft') {
            return back()->with('error', 'Jurnal sudah di-post');
        }

        if (!$jurnal->isBalanced()) {
            return back()->with('error', 'Jurnal tidak balance!');
        }

        try {
            DB::beginTransaction();

            $jurnal->update(['status' => 'posted']);

            // Update saldo perkiraan
            foreach ($jurnal->items as $item) {
                $this->updateSaldoPerkiraan(
                    $item->kode_perkiraan,
                    $item->debet,
                    $item->kredit
                );
            }

            DB::commit();

            return back()->with('success', 'Jurnal berhasil di-post');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal posting: ' . $e->getMessage());
        }
    }

    /**
     * Void jurnal
     */
    public function void(Jurnal $jurnal)
    {
        if ($jurnal->status === 'void') {
            return back()->with('error', 'Jurnal sudah void');
        }

        try {
            DB::beginTransaction();

            // Reverse saldo perkiraan jika sudah posted
            if ($jurnal->status === 'posted') {
                foreach ($jurnal->items as $item) {
                    $this->updateSaldoPerkiraan(
                        $item->kode_perkiraan,
                        -$item->debet, // Reverse
                        -$item->kredit  // Reverse
                    );
                }
            }

            $jurnal->update(['status' => 'void']);

            DB::commit();

            return back()->with('success', 'Jurnal berhasil di-void');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal void: ' . $e->getMessage());
        }
    }

    /**
     * Generate nomor bukti otomatis
     */
    private function generateNomorBukti()
    {
        $prefix = 'JU'; // Jurnal Umum
        $month = date('m');
        $year = date('y');
        
        $lastJurnal = Jurnal::where('nomor_bukti', 'LIKE', "{$prefix}/{$month}/{$year}/%")
            ->orderBy('nomor_bukti', 'desc')
            ->first();

        if ($lastJurnal) {
            $lastNumber = (int) substr($lastJurnal->nomor_bukti, -2);
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '01';
        }

        return "{$prefix}/{$month}/{$year}/{$newNumber}";
    }

    /**
     * Update saldo perkiraan
     */
    private function updateSaldoPerkiraan($kodePerkiraan, $debet, $kredit)
    {
        $perkiraan = Perkiraan::where('kode_perkiraan', $kodePerkiraan)->first();
        
        if ($perkiraan) {
            $perkiraan->increment('saldo_debet', $debet);
            $perkiraan->increment('saldo_kredit', $kredit);
        }
    }

    /**
     * Check balance AJAX
     */
    public function checkBalance(Request $request, $id)
    {
        $items = $request->items ?? [];
        
        $totalDebet = collect($items)->sum('debet');
        $totalKredit = collect($items)->sum('kredit');
        $balance = $totalDebet - $totalKredit;

        return response()->json([
            'total_debet' => $totalDebet,
            'total_kredit' => $totalKredit,
            'balance' => $balance,
            'is_balanced' => $balance == 0,
        ]);
    }

    /**
     * Search kavling for autocomplete
     */
    public function searchKavling(Request $request)
    {
        $search = $request->get('q');
        
        $kavling = ProjectLocation::where('kavling', 'LIKE', "%{$search}%")
            ->orWhere('blok', 'LIKE', "%{$search}%")
            ->limit(10)
            ->get(['kavling', 'blok', 'address']);

        return response()->json([
            'results' => $kavling->map(function($k) {
                return [
                    'id' => $k->kavling,
                    'text' => $k->kavling . ' - ' . $k->blok,
                ];
            })
        ]);
    }

    /**
     * Search user for autocomplete
     */
    public function searchUser(Request $request)
    {
        $search = $request->get('q');
        
        $users = User::where('full_name', 'LIKE', "%{$search}%")
            ->orWhere('username', 'LIKE', "%{$search}%")
            ->where('is_active', true)
            ->limit(10)
            ->get(['user_id', 'full_name', 'username']);

        return response()->json([
            'results' => $users->map(function($u) {
                return [
                    'id' => $u->user_id,
                    'text' => $u->full_name . ' (' . $u->username . ')',
                ];
            })
        ]);
    }

    /**
     * Print jurnal
     */
    public function print(Jurnal $jurnal)
    {
        $jurnal->load(['items.perkiraan', 'items.kavling', 'creator']);
        
        return view('accounting.jurnal.print', compact('jurnal'));
    }
}