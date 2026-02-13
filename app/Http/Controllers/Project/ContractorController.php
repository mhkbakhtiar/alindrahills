<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Contractor;
use Illuminate\Http\Request;

class ContractorController extends Controller
{
    public function index(Request $request)
    {
        $contractors = Contractor::when($request->search, function ($q) use ($request) {
                $q->where('contractor_name', 'like', '%' . $request->search . '%')
                  ->orWhere('contractor_code', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('contractors.index', compact('contractors'));
    }

    public function create()
    {
        return view('contractors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'contractor_code' => 'required|unique:contractors,contractor_code',
            'contractor_name' => 'required',
            'status'          => 'required',
        ]);

        Contractor::create($request->all());

        return redirect()
            ->route('contractors.index')
            ->with('success', 'Contractor berhasil ditambahkan');
    }

    public function edit(Contractor $contractor)
    {
        return view('contractors.edit', compact('contractor'));
    }

    public function update(Request $request, Contractor $contractor)
    {
        $request->validate([
            'contractor_code' => 'required|unique:contractors,contractor_code,' . $contractor->contractor_id . ',contractor_id',
            'contractor_name' => 'required',
            'status'          => 'required',
        ]);

        $contractor->update($request->all());

        return redirect()
            ->route('contractors.index')
            ->with('success', 'Contractor berhasil diperbarui');
    }

    public function destroy(Contractor $contractor)
    {
        $contractor->delete();

        return back()->with('success', 'Contractor berhasil dihapus');
    }
}
