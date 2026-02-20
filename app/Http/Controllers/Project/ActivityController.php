<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ProjectLocation;
use App\Models\Worker;
use App\Models\Contractor;
use App\Models\MasterPrefixNomor;
use App\Models\ActivityWorker;
use DB;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('location', 'creator');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('activity_name', 'like', '%' . $request->search . '%')
                  ->orWhere('activity_code', 'like', '%' . $request->search . '%');
            });
        }

        $activities = $query->latest()->paginate(20);

        return view('activities.index', compact('activities'));
    }

    public function create()
    {
        $locations = ProjectLocation::where('is_active', true)->get();
        $availableWorkers = Worker::where('is_active', true)->get();
        $contractors = Contractor::where('status', 'active')->get();
        
        return view('activities.create', compact('locations', 'availableWorkers', 'contractors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_name' => 'required|max:100',
            'location_id' => 'required|exists:project_locations,location_id',
            'activity_type' => 'required|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planned,ongoing,completed,cancelled',
            'description' => 'nullable',
            'workers' => 'nullable|array',
            'workers.*.worker_id' => 'required_with:workers|exists:workers,worker_id',
            'workers.*.assigned_date' => 'required_with:workers|date',
            'workers.*.work_description' => 'nullable',
            'contractors'   => 'array',
            'contractors.*' => 'exists:contractors,contractor_id',
        ]);

        DB::beginTransaction();
        try {
            $validated['created_by'] = auth()->id();
            $validated['activity_code'] = MasterPrefixNomor::generateFor('KEG');
            $activity = Activity::create($validated);

            // Assign workers if provided
            if (!empty($validated['workers'])) {
                foreach ($validated['workers'] as $worker) {
                    if (!empty($worker['worker_id'])) {
                        ActivityWorker::create([
                            'activity_id' => $activity->activity_id,
                            'worker_id' => $worker['worker_id'],
                            'assigned_date' => $worker['assigned_date'],
                            'work_description' => $worker['work_description'] ?? null,
                        ]);
                    }
                }
            }

            if ($request->has('contractors')) {
                $activity->contractors()->sync($request->contractors);
            }

            DB::commit();
            return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(Activity $activity)
    {
        $activity->load([
            'location',
            'creator',
            'contractors',        // ✅ INI PENTING
            'activityWorkers.worker'
        ]);

        return view('activities.show', compact('activity'));
    }


    public function edit(Activity $activity)
    {
        $locations = ProjectLocation::where('is_active', true)->get();
        $availableWorkers = Worker::where('is_active', true)->get();
        $activity->load('activityWorkers.worker');
        $activity->load('contractors');

        $contractors = Contractor::where('status', 'active')->get();
        return view('activities.edit', compact('activity', 'locations', 'availableWorkers', 'contractors'));
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'activity_name' => 'required|max:100',
            'location_id' => 'required|exists:project_locations,location_id',
            'activity_type' => 'required|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:planned,ongoing,completed,cancelled',
            'description' => 'nullable',
        ]);

        $activity->update($validated);

        return redirect()->route('activities.index')
            ->with('success', 'Kegiatan berhasil diupdate');
    }

    public function updateContractors(Request $request, Activity $activity)
    {
        $request->validate([
            'contractors'   => 'nullable|array',
            'contractors.*' => 'exists:contractors,contractor_id',
        ]);

        if ($request->filled('contractors')) {
            $activity->contractors()->sync($request->contractors);
        } else {
            $activity->contractors()->detach();
        }

        return back()->with('success', 'Contractor berhasil diperbarui');
    }



    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('activities.index')
            ->with('success', 'Kegiatan berhasil dihapus');
    }
}