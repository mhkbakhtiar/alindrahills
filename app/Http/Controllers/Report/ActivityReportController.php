<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ProjectLocation;
use App\Models\Contractor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ActivityReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('location', 'creator', 'contractors', 'activityWorkers.worker');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        // Filter by location
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by activity type
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        // Filter by contractor
        if ($request->filled('contractor_id')) {
            $query->whereHas('contractors', function($q) use ($request) {
                $q->where('contractor_id', $request->contractor_id);
            });
        }

        $activities = $query->latest('start_date')->get();

        // Summary statistics
        $summary = [
            'total_activities' => $activities->count(),
            'planned' => $activities->where('status', 'planned')->count(),
            'ongoing' => $activities->where('status', 'ongoing')->count(),
            'completed' => $activities->where('status', 'completed')->count(),
            'cancelled' => $activities->where('status', 'cancelled')->count(),
            'total_workers' => $activities->sum(function($activity) {
                return $activity->activityWorkers->count();
            }),
            'total_contractors' => $activities->sum(function($activity) {
                return $activity->contractors->count();
            }),
        ];

        // Group by location
        $byLocation = $activities->groupBy('location_id')->map(function($items) {
            return [
                'location' => $items->first()->location->location_name ?? 'N/A',
                'count' => $items->count(),
                'planned' => $items->where('status', 'planned')->count(),
                'ongoing' => $items->where('status', 'ongoing')->count(),
                'completed' => $items->where('status', 'completed')->count(),
            ];
        });

        // Group by activity type
        $byType = $activities->groupBy('activity_type')->map(function($items, $type) {
            return [
                'type' => $type,
                'count' => $items->count(),
            ];
        });

        // Data for filters
        $locations = ProjectLocation::where('is_active', true)->get();
        $contractors = Contractor::where('status', 'active')->get();
        $activityTypes = Activity::distinct()->pluck('activity_type');

        return view('reports.activities.index', compact(
            'activities',
            'summary',
            'byLocation',
            'byType',
            'locations',
            'contractors',
            'activityTypes'
        ));
    }

    public function export(Request $request)
    {
        $query = Activity::with('location', 'creator', 'contractors', 'activityWorkers.worker');

        // Apply same filters as index
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }
        if ($request->filled('contractor_id')) {
            $query->whereHas('contractors', function($q) use ($request) {
                $q->where('contractor_id', $request->contractor_id);
            });
        }

        $activities = $query->latest('start_date')->get();

        $summary = [
            'total_activities' => $activities->count(),
            'planned' => $activities->where('status', 'planned')->count(),
            'ongoing' => $activities->where('status', 'ongoing')->count(),
            'completed' => $activities->where('status', 'completed')->count(),
            'cancelled' => $activities->where('status', 'cancelled')->count(),
            'total_workers' => $activities->sum(function($activity) {
                return $activity->activityWorkers->count();
            }),
            'total_contractors' => $activities->sum(function($activity) {
                return $activity->contractors->count();
            }),
        ];

        $pdf = Pdf::loadView('reports.activities.export', compact('activities', 'summary'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        $fileName = 'Laporan_Kegiatan_' . Carbon::now()->format('YmdHis') . '.pdf';

        return $pdf->download($fileName);
    }

    public function detail(Activity $activity)
    {
        $activity->load([
            'location',
            'creator',
            'contractors',
            'activityWorkers.worker'
        ]);

        $pdf = Pdf::loadView('reports.activities.detail', compact('activity'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        $fileName = 'Detail_Kegiatan_' . $activity->activity_code . '.pdf';

        return $pdf->download($fileName);
    }
}