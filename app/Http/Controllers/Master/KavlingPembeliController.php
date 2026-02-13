<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KavlingPembeli;
use App\Models\ProjectLocation;
use App\Models\Pembeli;

class KavlingPembeliController extends Controller
{
    public function index()
    {
        $kavlingPembeli = KavlingPembeli::with(['kavling', 'pembeli'])
            ->latest()
            ->paginate(20);
            
        return view('master.kavling-pembeli.index', compact('kavlingPembeli'));
    }

    public function create()
    {
        $kavlings = ProjectLocation::where('is_active', true)->get();
        $pembeli = Pembeli::where('is_active', true)->get();
        
        return view('master.kavling-pembeli.create', compact('kavlings', 'pembeli'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:project_locations,location_id',
            'user_id' => 'required|exists:pembeli,user_id',
            'tanggal_booking' => 'nullable|date',
            'tanggal_akad' => 'nullable|date',
            'harga_jual' => 'nullable|numeric|min:0',
            'status' => 'required|in:booking,akad,lunas,batal',
            'keterangan' => 'nullable',
        ]);

        KavlingPembeli::create($validated);

        return redirect()->route('master.kavling-pembeli.index')
            ->with('success', 'Pengaitan kavling dan pembeli berhasil ditambahkan');
    }

    public function show(KavlingPembeli $kavlingPembeli)
    {
        $kavlingPembeli->load(['kavling', 'pembeli']);
        return view('master.kavling-pembeli.show', compact('kavlingPembeli'));
    }

    public function edit(KavlingPembeli $kavlingPembeli)
    {
        $kavlings = ProjectLocation::where('is_active', true)->get();
        $pembeli = Pembeli::where('is_active', true)->get();
        
        $kavlingPembeli->load(['kavling', 'pembeli']);
        
        return view('master.kavling-pembeli.edit', compact('kavlingPembeli', 'kavlings', 'pembeli'));
    }

    public function update(Request $request, KavlingPembeli $kavlingPembeli)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:project_locations,location_id',
            'user_id' => 'required|exists:pembeli,user_id',
            'tanggal_booking' => 'nullable|date',
            'tanggal_akad' => 'nullable|date',
            'harga_jual' => 'nullable|numeric|min:0',
            'status' => 'required|in:booking,akad,lunas,batal',
            'keterangan' => 'nullable',
        ]);

        $kavlingPembeli->update($validated);

        return redirect()->route('master.kavling-pembeli.index')
            ->with('success', 'Pengaitan kavling dan pembeli berhasil diupdate');
    }

    public function destroy(KavlingPembeli $kavlingPembeli)
    {
        $kavlingPembeli->delete();

        return redirect()->route('master.kavling-pembeli.index')
            ->with('success', 'Pengaitan kavling dan pembeli berhasil dihapus');
    }
}