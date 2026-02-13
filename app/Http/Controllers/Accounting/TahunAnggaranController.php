<?php

// =============================================================================
// TahunAnggaranController
// File: app/Http/Controllers/Accounting/TahunAnggaranController.php
// =============================================================================

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\TahunAnggaran;
use Illuminate\Http\Request;

class TahunAnggaranController extends Controller
{
    public function index()
    {
        $tahunAnggaran = TahunAnggaran::orderBy('tahun', 'desc')->paginate(10);
        return view('accounting.tahun-anggaran.index', compact('tahunAnggaran'));
    }

    public function create()
    {
        return view('accounting.tahun-anggaran.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|unique:tahun_anggaran,tahun|digits:4',
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after:periode_awal',
            'keterangan' => 'nullable',
        ]);

        TahunAnggaran::create($validated);

        return redirect()
            ->route('accounting.tahun-anggaran.index')
            ->with('success', 'Tahun anggaran berhasil ditambahkan');
    }

    public function show(TahunAnggaran $tahunAnggaran)
    {
        $tahunAnggaran->load('jurnal');
        return view('accounting.tahun-anggaran.show', compact('tahunAnggaran'));
    }

    public function edit(TahunAnggaran $tahunAnggaran)
    {
        if ($tahunAnggaran->status === 'tutup_buku') {
            return back()->with('error', 'Tahun anggaran yang sudah tutup buku tidak dapat diedit');
        }

        return view('accounting.tahun-anggaran.edit', compact('tahunAnggaran'));
    }

    public function update(Request $request, TahunAnggaran $tahunAnggaran)
    {
        if ($tahunAnggaran->status === 'tutup_buku') {
            return back()->with('error', 'Tahun anggaran yang sudah tutup buku tidak dapat diedit');
        }

        $validated = $request->validate([
            'tahun' => 'required|digits:4|unique:tahun_anggaran,tahun,' . $tahunAnggaran->id,
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after:periode_awal',
            'keterangan' => 'nullable',
        ]);

        $tahunAnggaran->update($validated);

        return redirect()
            ->route('accounting.tahun-anggaran.index')
            ->with('success', 'Tahun anggaran berhasil diupdate');
    }

    public function destroy(TahunAnggaran $tahunAnggaran)
    {
        if ($tahunAnggaran->jurnal()->exists()) {
            return back()->with('error', 'Tahun anggaran tidak dapat dihapus karena sudah memiliki transaksi');
        }

        $tahunAnggaran->delete();

        return redirect()
            ->route('accounting.tahun-anggaran.index')
            ->with('success', 'Tahun anggaran berhasil dihapus');
    }

    /**
     * Activate tahun anggaran (set as active, deactivate others)
     */
    public function activate($id)
    {
        TahunAnggaran::query()->update(['status' => 'tutup_buku']);
        
        $tahunAnggaran = TahunAnggaran::findOrFail($id);
        $tahunAnggaran->update(['status' => 'aktif']);

        return back()->with('success', 'Tahun anggaran ' . $tahunAnggaran->tahun . ' berhasil diaktifkan');
    }

    /**
     * Close tahun anggaran (tutup buku)
     */
    public function close($id)
    {
        $tahunAnggaran = TahunAnggaran::findOrFail($id);
        
        if ($tahunAnggaran->status === 'tutup_buku') {
            return back()->with('error', 'Tahun anggaran sudah tutup buku');
        }

        // TODO: Create closing entries (jurnal penutup)
        
        $tahunAnggaran->update(['status' => 'tutup_buku']);

        return back()->with('success', 'Tahun anggaran berhasil ditutup');
    }
}