<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembeli;

class PembeliController extends Controller
{
    public function index()
    {
        $pembeli = Pembeli::withCount('kavlings')->paginate(20);
        return view('master.pembeli.index', compact('pembeli'));
    }

    public function create()
    {
        return view('master.pembeli.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|max:100',
            'email' => 'nullable|email|max:100',
            'telepon' => 'nullable|max:20',
            'alamat' => 'nullable',
            'no_identitas' => 'nullable|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        Pembeli::create($validated);

        return redirect()->route('master.pembeli.index')
            ->with('success', 'Data pembeli berhasil ditambahkan');
    }

    public function show(Pembeli $pembeli)
    {
        $pembeli->load(['kavlings' => function($q) {
            $q->withPivot(['tanggal_booking', 'tanggal_akad', 'harga_jual', 'status', 'keterangan']);
        }]);
        
        return view('master.pembeli.show', compact('pembeli'));
    }

    public function edit(Pembeli $pembeli)
    {
        return view('master.pembeli.edit', compact('pembeli'));
    }

    public function update(Request $request, Pembeli $pembeli)
    {
        $validated = $request->validate([
            'nama' => 'required|max:100',
            'email' => 'nullable|email|max:100',
            'telepon' => 'nullable|max:20',
            'alamat' => 'nullable',
            'no_identitas' => 'nullable|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $pembeli->update($validated);

        return redirect()->route('master.pembeli.index')
            ->with('success', 'Data pembeli berhasil diupdate');
    }

    public function destroy(Pembeli $pembeli)
    {
        // Cek apakah ada kavling terkait
        if ($pembeli->kavlings()->wherePivot('status', '!=', 'batal')->count() > 0) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus pembeli yang masih memiliki kavling aktif']);
        }

        $pembeli->delete();

        return redirect()->route('master.pembeli.index')
            ->with('success', 'Data pembeli berhasil dihapus');
    }
}