<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use App\Models\MasterPrefixNomor;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    public function index(Request $request)
    {
        $query = Worker::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('worker_code', 'like', '%' . $request->search . '%');
            });
        }

        $workers = $query->paginate(20);

        return view('workers.index', compact('workers'));
    }

    public function create()
    {
        return view('workers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|max:100',
            'phone' => 'nullable|max:20',
            'worker_type' => 'required|max:50',
            'daily_rate' => 'required|numeric|min:0',
        ]);

        $validated['worker_code'] = MasterPrefixNomor::generateFor('TKG');

        Worker::create($validated);

        return redirect()->route('workers.index')
            ->with('success', 'Data tukang berhasil ditambahkan');
    }

    public function edit(Worker $worker)
    {
        return view('workers.edit', compact('worker'));
    }

    public function update(Request $request, Worker $worker)
    {
        $validated = $request->validate([
            'full_name' => 'required|max:100',
            'phone' => 'nullable|max:20',
            'worker_type' => 'required|max:50',
            'daily_rate' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $worker->update($validated);

        return redirect()->route('workers.index')
            ->with('success', 'Data tukang berhasil diupdate');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
