<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\ActivityWorker;
use Illuminate\Http\Request;

class ActivityWorkerController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:activities,activity_id',
            'worker_id' => 'required|exists:workers,worker_id',
            'assigned_date' => 'required|date',
            'work_description' => 'nullable|max:255',
        ]);

        // Check if worker already assigned
        $exists = ActivityWorker::where('activity_id', $validated['activity_id'])
            ->where('worker_id', $validated['worker_id'])
            ->where('is_active', true)
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'Tukang sudah ditugaskan ke kegiatan ini']);
        }

        ActivityWorker::create($validated);

        return back()->with('success', 'Tukang berhasil ditugaskan');
    }

    public function destroy(ActivityWorker $activityWorker)
    {
        $activityWorker->update(['is_active' => false]);
        
        return back()->with('success', 'Penugasan tukang berhasil dihapus');
    }
}