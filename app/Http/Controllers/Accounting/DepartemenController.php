<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\User;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function index()
    {
        $departemen = Departemen::with('kepala')->paginate(10);
        return view('accounting.departemen.index', compact('departemen'));
    }

    public function create()
    {
        $users = User::where('is_active', true)->get();
        return view('accounting.departemen.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_departemen' => 'required|unique:departemen,kode_departemen|max:10',
            'nama_departemen' => 'required|max:100',
            'deskripsi' => 'nullable',
            'kepala_departemen' => 'nullable|exists:users,user_id',
        ]);

        Departemen::create($validated);

        return redirect()
            ->route('accounting.departemen.index')
            ->with('success', 'Departemen berhasil ditambahkan');
    }

    public function show(Departemen $departemen)
    {
        $departemen->load('kepala');
        return view('accounting.departemen.show', compact('departemen'));
    }

    public function edit(Departemen $departemen)
    {
        $users = User::where('is_active', true)->get();
        return view('accounting.departemen.edit', compact('departemen', 'users'));
    }

    public function update(Request $request, Departemen $departemen)
    {
        $validated = $request->validate([
            'kode_departemen' => 'required|max:10|unique:departemen,kode_departemen,' . $departemen->id,
            'nama_departemen' => 'required|max:100',
            'deskripsi' => 'nullable',
            'kepala_departemen' => 'nullable|exists:users,user_id',
            'is_active' => 'boolean',
        ]);

        $departemen->update($validated);

        return redirect()
            ->route('accounting.departemen.index')
            ->with('success', 'Departemen berhasil diupdate');
    }

    public function destroy(Departemen $departemen)
    {
        // Check if departemen is used
        // You can add more checks here if needed
        
        $departemen->delete();

        return redirect()
            ->route('accounting.departemen.index')
            ->with('success', 'Departemen berhasil dihapus');
    }
}