<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\MasterPrefixNomor;
use Illuminate\Http\Request;

class MasterPrefixNomorController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterPrefixNomor::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('kode_jenis', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_jenis', 'like', '%' . $request->search . '%')
                  ->orWhere('prefix', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $prefixes = $query->orderBy('kode_jenis')->paginate(15)->withQueryString();

        return view('settings.prefix.index', compact('prefixes'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        return view('settings.prefix.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'kode_jenis'     => 'required|max:50|unique:master_prefix_nomor,kode_jenis|alpha_dash|uppercase',
            'nama_jenis'     => 'required|max:150',
            'prefix'         => 'required|max:20',
            'format_tahun'   => 'required|in:YYYY,YY,none',
            'format_bulan'   => 'required|in:MM,none',
            'separator'      => 'required|in:/,-,.,',
            'panjang_urutan' => 'required|integer|min:1|max:10',
            'reset_per'      => 'required|in:tahun,bulan,never',
            'keterangan'     => 'nullable',
            'is_active'      => 'boolean',
        ], [
            'kode_jenis.alpha_dash' => 'Kode jenis hanya boleh mengandung huruf, angka, dan underscore.',
            'kode_jenis.unique'     => 'Kode jenis sudah digunakan.',
        ]);

        $prefix = MasterPrefixNomor::create(array_merge($validated, [
            'nomor_terakhir' => 0,
            'is_active'      => $request->boolean('is_active', true),
        ]));

        // Auto-generate contoh hasil
        $prefix->update(['contoh_hasil' => $prefix->buildContoh()]);

        return redirect()->route('settings.prefix.index')
            ->with('success', "Prefix <strong>{$prefix->kode_jenis}</strong> berhasil ditambahkan.");
    }

    public function show(MasterPrefixNomor $prefix)
    {
        return view('settings.prefix.show', compact('prefix'));
    }

    public function edit(MasterPrefixNomor $prefix)
    {
        $this->authorizeAdmin();
        return view('settings.prefix.edit', compact('prefix'));
    }

    public function update(Request $request, MasterPrefixNomor $prefix)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'nama_jenis'     => 'required|max:150',
            'prefix'         => 'required|max:20',
            'format_tahun'   => 'required|in:YYYY,YY,none',
            'format_bulan'   => 'required|in:MM,none',
            'separator'      => 'required|in:/,-,.,',
            'panjang_urutan' => 'required|integer|min:1|max:10',
            'reset_per'      => 'required|in:tahun,bulan,never',
            'keterangan'     => 'nullable',
            'is_active'      => 'boolean',
        ]);

        $prefix->update(array_merge($validated, [
            'is_active' => $request->boolean('is_active'),
        ]));

        // Rebuild contoh
        $prefix->update(['contoh_hasil' => $prefix->buildContoh()]);

        return redirect()->route('settings.prefix.index')
            ->with('success', "Prefix <strong>{$prefix->kode_jenis}</strong> berhasil diperbarui.");
    }

    public function destroy(MasterPrefixNomor $prefix)
    {
        $this->authorizeAdmin();

        if ($prefix->nomor_terakhir > 0) {
            return back()->with('error',
                "Prefix <strong>{$prefix->kode_jenis}</strong> tidak dapat dihapus karena sudah pernah digunakan (nomor terakhir: {$prefix->nomor_terakhir}).");
        }

        $kode = $prefix->kode_jenis;
        $prefix->delete();

        return redirect()->route('settings.prefix.index')
            ->with('success', "Prefix <strong>{$kode}</strong> berhasil dihapus.");
    }

    /**
     * Reset nomor urut ke 0 (untuk keperluan testing atau koreksi data).
     */
    public function reset(MasterPrefixNomor $prefix)
    {
        $this->authorizeAdmin();

        $prefix->update(['nomor_terakhir' => 0]);

        return back()->with('success',
            "Nomor urut prefix <strong>{$prefix->kode_jenis}</strong> berhasil direset ke 0.");
    }

    /**
     * Preview nomor berikutnya via AJAX.
     */
    public function preview(Request $request)
    {
        $validated = $request->validate([
            'prefix'         => 'required|max:20',
            'format_tahun'   => 'required|in:YYYY,YY,none',
            'format_bulan'   => 'required|in:MM,none',
            'separator'      => 'required',
            'panjang_urutan' => 'required|integer|min:1|max:10',
        ]);

        $temp = new MasterPrefixNomor($validated);
        return response()->json(['contoh' => $temp->buildContoh()]);
    }

    private function authorizeAdmin(): void
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperadmin()) {
            abort(403, 'Unauthorized action.');
        }
    }
}