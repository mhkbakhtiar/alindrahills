<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('material_name', 'like', '%' . $request->search . '%')
                  ->orWhere('material_code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $materials = $query->with('warehouseStocks')
            ->paginate(20);

        $categories = Material::distinct()->pluck('category');

        return view('materials.index', compact('materials', 'categories'));
    }

    public function create()
    {
        return view('materials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'material_code' => 'required|unique:materials|max:20',
            'material_name' => 'required|max:100',
            'category' => 'required|max:50',
            'unit' => 'required|max:20',
            'min_stock' => 'nullable|numeric|min:0',
            'costing_method' => 'required|in:FIFO,LIFO,AVERAGE',
            'description' => 'nullable',
        ]);

        Material::create($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Material berhasil ditambahkan');
    }

    public function show(Material $material)
    {
        $material->load([
            'warehouseStocks.warehouse',
            'batches' => function($query) {
                $query->where('status', 'active')->orderBy('purchase_date', 'asc');
            }
        ]);

        return view('materials.show', compact('material'));
    }

    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'material_code' => 'required|max:20|unique:materials,material_code,' . $material->material_id . ',material_id',
            'material_name' => 'required|max:100',
            'category' => 'required|max:50',
            'unit' => 'required|max:20',
            'min_stock' => 'nullable|numeric|min:0',
            'costing_method' => 'required|in:FIFO,LIFO,AVERAGE',
            'description' => 'nullable',
        ]);

        $material->update($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Material berhasil diupdate');
    }

    public function destroy(Material $material)
    {
        $material->update(['is_active' => false]);

        return redirect()->route('materials.index')
            ->with('success', 'Material berhasil dinonaktifkan');
    }
}
