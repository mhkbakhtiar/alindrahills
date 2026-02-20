<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\PayrollRequest;
use App\Models\PayrollRequestDetail;
use App\Models\Worker;
use App\Models\MasterPrefixNomor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PayrollRequest::with('requester', 'approver');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if (auth()->user()->isTeknik() || (auth()->user()->isSuperadmin())) {
            $query->where('requested_by', auth()->id());
        }

        $requests = $query->latest()->paginate(20);

        return view('payroll.index', compact('requests'));
    }

    public function create()
    {
        $workers = Worker::where('is_active', true)->get();
        
        // Get activities yang sedang berlangsung atau baru selesai (dalam 30 hari terakhir)
        $activities = Activity::with(['location', 'activityWorkers.worker'])
            ->where(function($query) {
                $query->where('status', 'ongoing')
                      ->orWhere(function($q) {
                          $q->where('status', 'completed')
                            ->where('end_date', '>=', now()->subDays(30));
                      });
            })
            ->orderBy('start_date', 'desc')
            ->get();
        
        return view('payroll.create', compact('workers', 'activities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_date' => 'required|date',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'letter_date' => 'required|date',
            'activity_id' => 'nullable|exists:activities,activity_id',
            'notes' => 'nullable',
            'workers' => 'required|array|min:1',
            'workers.*.worker_id' => 'required|exists:workers,worker_id',
            'workers.*.days_worked' => 'required|numeric|min:0.5',
            'workers.*.daily_rate' => 'required|numeric|min:0',
            'workers.*.total_wage' => 'required|numeric|min:0',
            'workers.*.bonus' => 'nullable|numeric|min:0',
            'workers.*.deduction' => 'nullable|numeric|min:0',
            'workers.*.net_payment' => 'required|numeric|min:0',
            'workers.*.notes' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $payrollRequest = PayrollRequest::create([
                'request_number' => $this->generateRequestNumber(),
                'request_date' => $validated['request_date'],
                'period_start' => $validated['period_start'],
                'period_end' => $validated['period_end'],
                'requested_by' => auth()->id(),
                'activity_id' => $validated['activity_id'] ?? null,
                'letter_number' => MasterPrefixNomor::generateFor('GJI'),
                'letter_date' => $validated['letter_date'],
                'notes' => $validated['notes'],
                'status' => 'pending',
            ]);

            $totalAmount = 0;

            foreach ($validated['workers'] as $worker) {
                PayrollRequestDetail::create([
                    'payroll_request_id' => $payrollRequest->payroll_request_id,
                    'worker_id' => $worker['worker_id'],
                    'days_worked' => $worker['days_worked'],
                    'daily_rate' => $worker['daily_rate'],
                    'total_wage' => $worker['total_wage'],
                    'bonus' => $worker['bonus'] ?? 0,
                    'deduction' => $worker['deduction'] ?? 0,
                    'net_payment' => $worker['net_payment'],
                    'notes' => $worker['notes'] ?? null,
                ]);

                $totalAmount += $worker['net_payment'];
            }

            $payrollRequest->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('payroll-requests.index')
                ->with('success', 'Pengajuan penggajian berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Gagal membuat pengajuan: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(PayrollRequest $payrollRequest)
    {
        $payrollRequest->load('requester', 'approver', 'details.worker', 'activity.location');
        return view('payroll.show', compact('payrollRequest'));
    }

    public function edit(PayrollRequest $payrollRequest)
    {
        // Only allow edit if status is pending
        if ($payrollRequest->status !== 'pending') {
            return redirect()->route('payroll-requests.show', $payrollRequest)
                ->with('error', 'Pengajuan tidak dapat diubah karena sudah diproses');
        }

        $workers = Worker::where('is_active', true)->get();
        
        $activities = Activity::with(['location', 'activityWorkers.worker'])
            ->where(function($query) {
                $query->where('status', 'ongoing')
                      ->orWhere(function($q) {
                          $q->where('status', 'completed')
                            ->where('end_date', '>=', now()->subDays(30));
                      });
            })
            ->orderBy('start_date', 'desc')
            ->get();

        $payrollRequest->load('details.worker');

        return view('payroll.edit', compact('payrollRequest', 'workers', 'activities'));
    }

    public function update(Request $request, PayrollRequest $payrollRequest)
    {
        // Only allow update if status is pending
        if ($payrollRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'Pengajuan tidak dapat diubah karena sudah diproses']);
        }

        $validated = $request->validate([
            'request_date' => 'required|date',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'letter_date' => 'required|date',
            'activity_id' => 'nullable|exists:activities,activity_id',
            'notes' => 'nullable',
            'workers' => 'required|array|min:1',
            'workers.*.worker_id' => 'required|exists:workers,worker_id',
            'workers.*.days_worked' => 'required|numeric|min:0.5',
            'workers.*.daily_rate' => 'required|numeric|min:0',
            'workers.*.total_wage' => 'required|numeric|min:0',
            'workers.*.bonus' => 'nullable|numeric|min:0',
            'workers.*.deduction' => 'nullable|numeric|min:0',
            'workers.*.net_payment' => 'required|numeric|min:0',
            'workers.*.notes' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            // Update payroll request
            $payrollRequest->update([
                'request_date' => $validated['request_date'],
                'period_start' => $validated['period_start'],
                'period_end' => $validated['period_end'],
                'activity_id' => $validated['activity_id'] ?? null,
                'letter_date' => $validated['letter_date'],
                'notes' => $validated['notes'],
            ]);

            // Delete existing details
            $payrollRequest->details()->delete();

            $totalAmount = 0;

            // Create new details
            foreach ($validated['workers'] as $worker) {
                PayrollRequestDetail::create([
                    'payroll_request_id' => $payrollRequest->payroll_request_id,
                    'worker_id' => $worker['worker_id'],
                    'days_worked' => $worker['days_worked'],
                    'daily_rate' => $worker['daily_rate'],
                    'total_wage' => $worker['total_wage'],
                    'bonus' => $worker['bonus'] ?? 0,
                    'deduction' => $worker['deduction'] ?? 0,
                    'net_payment' => $worker['net_payment'],
                    'notes' => $worker['notes'] ?? null,
                ]);

                $totalAmount += $worker['net_payment'];
            }

            $payrollRequest->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('payroll-requests.show', $payrollRequest)
                ->with('success', 'Pengajuan penggajian berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Gagal mengupdate pengajuan: ' . $e->getMessage()])->withInput();
        }
    }

    public function approve(Request $request, $id)
    {
        $payrollRequest = PayrollRequest::findOrFail($id);

        if (!auth()->user()->isAdmin() || $payrollRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'Pengajuan tidak dapat diapprove']);
        }

        $payrollRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_date' => now(),
        ]);

        return back()->with('success', 'Pengajuan penggajian berhasil diapprove');
    }

    /**
     * Get workers from activity (AJAX endpoint)
     */
    public function getActivityWorkers(Request $request, $activityId)
    {
        $activity = Activity::with(['activityWorkers' => function($query) {
            $query->where('is_active', true);
        }, 'activityWorkers.worker'])
        ->findOrFail($activityId);

        $workers = $activity->activityWorkers->map(function($activityWorker) use ($activity) {
            return [
                'worker_id' => $activityWorker->worker_id,
                'full_name' => $activityWorker->worker->full_name,
                'daily_rate' => $activityWorker->worker->daily_rate,
                'days_worked' => $activityWorker->days_worked ?? 1, // Default 1 jika tidak ada
            ];
        });

        return response()->json([
            'success' => true,
            'activity' => [
                'activity_code' => $activity->activity_code,
                'activity_name' => $activity->activity_name,
                'start_date' => $activity->start_date->format('Y-m-d'),
                'end_date' => $activity->end_date ? $activity->end_date->format('Y-m-d') : null,
            ],
            'workers' => $workers
        ]);
    }

    private function generateRequestNumber()
    {
        $date = date('Ymd');
        $count = PayrollRequest::whereDate('created_at', today())->count() + 1;
        return 'PAY-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Print invoice/slip gaji PDF
     */
    public function printInvoice(PayrollRequest $payrollRequest)
    {
        $payrollRequest->load('requester', 'approver', 'details.worker', 'activity.location');

        // Generate PDF
        $pdf = Pdf::loadView('payroll.invoice', compact('payrollRequest'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        $fileName = 'Invoice_Payroll_' . $payrollRequest->request_number . '.pdf';

        // Return PDF for download
        return $pdf->download($fileName);
        
        // OR return for inline view (di browser)
        // return $pdf->stream($fileName);
    }

    /**
     * Print individual worker slip
     */
    public function printWorkerSlip(PayrollRequest $payrollRequest, $workerId)
    {
        $payrollRequest->load('requester', 'approver', 'activity.location');
        
        $detail = $payrollRequest->details()
            ->with('worker')
            ->where('worker_id', $workerId)
            ->firstOrFail();

        $pdf = Pdf::loadView('payroll.worker-slip', compact('payrollRequest', 'detail'))
            ->setPaper('a4', 'portrait');

        $fileName = 'Slip_' . $detail->worker->full_name . '_' . $payrollRequest->request_number . '.pdf';

        return $pdf->download($fileName);
    }
}