<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectLocation;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = ProjectLocation::paginate(20);
        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kavling' => 'required|max:50',
            'blok' => 'required|max:50',
            'address' => 'nullable',
            'is_active' => 'nullable|boolean',
        ]);

        ProjectLocation::create($validated);

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi proyek berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectLocation $location)
    {
        $location->load('activities');
        return view('locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectLocation $location)
    {
        $location->load('activities');
        return view('locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectLocation $location)
    {
        $validated = $request->validate([
            'kavling' => 'required|max:50',
            'blok' => 'required|max:50',
            'address' => 'nullable',
            'is_active' => 'nullable|boolean',
        ]);

        $location->update($validated);

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi proyek berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectLocation $location)
    {
        // Check if location has activities
        if ($location->activities()->count() > 0) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus lokasi yang memiliki kegiatan. Total kegiatan: ' . $location->activities()->count()]);
        }

        $location->delete();

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi proyek berhasil dihapus');
    }
}
