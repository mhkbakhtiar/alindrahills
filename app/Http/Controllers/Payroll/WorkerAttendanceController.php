<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use App\Models\WorkerAttendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WorkerAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = WorkerAttendance::with('worker');

        // Filter by date
        if ($request->filled('date')) {
            $query->byDate($request->date);
        } else {
            // Default to today
            $query->byDate(Carbon::today());
        }

        // Filter by worker
        if ($request->filled('worker_id')) {
            $query->where('worker_id', $request->worker_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $attendances = $query->orderBy('attendance_date', 'desc')
                             ->orderBy('check_in', 'asc')
                             ->paginate(20);

        $workers = Worker::where('is_active', true)
                        ->orderBy('full_name')
                        ->get();

        $selectedDate = $request->filled('date') ? $request->date : Carbon::today()->format('Y-m-d');

        return view('attendances.index', compact('attendances', 'workers', 'selectedDate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $workers = Worker::where('is_active', true)
                        ->orderBy('full_name')
                        ->get();
        
        return view('attendances.create', compact('workers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'worker_id' => 'required|exists:workers,worker_id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'hours_worked' => 'nullable|numeric|min:0|max:24',
            'notes' => 'nullable|string',
        ]);

        // Check duplicate
        $exists = WorkerAttendance::where('worker_id', $validated['worker_id'])
                                  ->where('attendance_date', $validated['attendance_date'])
                                  ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Absensi untuk tukang ini pada tanggal tersebut sudah ada');
        }

        $attendance = WorkerAttendance::create($validated);

        // Auto calculate hours if not provided
        if (!$validated['hours_worked'] && $validated['check_in'] && $validated['check_out']) {
            $attendance->calculateHoursWorked();
        }

        return redirect()->route('attendances.index')
            ->with('success', 'Data absensi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkerAttendance $attendance)
    {
        $attendance->load('worker');
        
        return view('attendances.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkerAttendance $attendance)
    {
        $workers = Worker::where('is_active', true)
                        ->orderBy('full_name')
                        ->get();
        
        return view('attendances.edit', compact('attendance', 'workers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkerAttendance $attendance)
    {
        $validated = $request->validate([
            'worker_id' => 'required|exists:workers,worker_id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'hours_worked' => 'nullable|numeric|min:0|max:24',
            'notes' => 'nullable|string',
        ]);

        // Check duplicate (exclude current record)
        $exists = WorkerAttendance::where('worker_id', $validated['worker_id'])
                                  ->where('attendance_date', $validated['attendance_date'])
                                  ->where('attendance_id', '!=', $attendance->attendance_id)
                                  ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Absensi untuk tukang ini pada tanggal tersebut sudah ada');
        }

        $attendance->update($validated);

        // Auto calculate hours if not provided
        if (!$validated['hours_worked'] && $validated['check_in'] && $validated['check_out']) {
            $attendance->calculateHoursWorked();
        }

        return redirect()->route('attendances.index')
            ->with('success', 'Data absensi berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkerAttendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('attendances.index')
            ->with('success', 'Data absensi berhasil dihapus');
    }

    /**
     * Bulk create attendance for all active workers
     */
    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'attendance_date' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpha',
        ]);

        $workers = Worker::where('is_active', true)->get();
        $created = 0;

        foreach ($workers as $worker) {
            $exists = WorkerAttendance::where('worker_id', $worker->worker_id)
                                      ->where('attendance_date', $validated['attendance_date'])
                                      ->exists();

            if (!$exists) {
                WorkerAttendance::create([
                    'worker_id' => $worker->worker_id,
                    'attendance_date' => $validated['attendance_date'],
                    'status' => $validated['status'],
                ]);
                $created++;
            }
        }

        return redirect()->route('attendances.index', ['date' => $validated['attendance_date']])
            ->with('success', "Berhasil membuat {$created} data absensi");
    }

    /**
     * Show monthly report
     */
    public function monthlyReport(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $attendances = WorkerAttendance::with('worker')
                                       ->byMonth($year, $month)
                                       ->get();

        $workers = Worker::where('is_active', true)
                        ->orderBy('full_name')
                        ->get();

        // Group by worker
        $report = [];
        foreach ($workers as $worker) {
            $workerAttendances = $attendances->where('worker_id', $worker->worker_id);
            
            $report[] = [
                'worker' => $worker,
                'total_days' => $workerAttendances->count(),
                'hadir' => $workerAttendances->where('status', 'hadir')->count(),
                'izin' => $workerAttendances->where('status', 'izin')->count(),
                'sakit' => $workerAttendances->where('status', 'sakit')->count(),
                'alpha' => $workerAttendances->where('status', 'alpha')->count(),
                'total_hours' => $workerAttendances->sum('hours_worked'),
            ];
        }

        return view('attendances.monthly-report', compact('report', 'year', 'month'));
    }

    /**
     * Export monthly report to PDF
     */
    public function exportPdf(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $attendances = WorkerAttendance::with('worker')
                                       ->byMonth($year, $month)
                                       ->get();

        $workers = Worker::where('is_active', true)
                        ->orderBy('full_name')
                        ->get();

        // Group by worker
        $report = [];
        foreach ($workers as $worker) {
            $workerAttendances = $attendances->where('worker_id', $worker->worker_id);
            
            $report[] = [
                'worker' => $worker,
                'total_days' => $workerAttendances->count(),
                'hadir' => $workerAttendances->where('status', 'hadir')->count(),
                'izin' => $workerAttendances->where('status', 'izin')->count(),
                'sakit' => $workerAttendances->where('status', 'sakit')->count(),
                'alpha' => $workerAttendances->where('status', 'alpha')->count(),
                'total_hours' => $workerAttendances->sum('hours_worked'),
            ];
        }

        $pdf = \PDF::loadView('attendances.monthly-report-pdf', compact('report', 'year', 'month'));
        
        $monthName = Carbon::createFromDate($year, $month, 1)->format('F');
        $filename = "Laporan_Absensi_{$monthName}_{$year}.pdf";
        
        return $pdf->download($filename);
    }
}