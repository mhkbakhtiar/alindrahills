<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\PayrollRequest;
use App\Models\Worker;
use App\Models\Activity;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollReportController extends Controller
{
    public function index(Request $request)
    {
        $query = PayrollRequest::with('requester', 'approver', 'details.worker', 'activity');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('period_start', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('period_end', '<=', $request->end_date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by activity
        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        // Filter by requester
        if ($request->filled('requested_by')) {
            $query->where('requested_by', $request->requested_by);
        }

        $payrolls = $query->latest('request_date')->get();

        // Summary statistics
        $summary = [
            'total_requests' => $payrolls->count(),
            'pending' => $payrolls->where('status', 'pending')->count(),
            'approved' => $payrolls->where('status', 'approved')->count(),
            'rejected' => $payrolls->where('status', 'rejected')->count(),
            'paid' => $payrolls->where('status', 'paid')->count(),
            'total_amount' => $payrolls->sum('total_amount'),
            'approved_amount' => $payrolls->where('status', 'approved')->sum('total_amount'),
            'paid_amount' => $payrolls->where('status', 'paid')->sum('total_amount'),
            'total_workers' => $payrolls->sum(function($payroll) {
                return $payroll->details->count();
            }),
        ];

        // Group by month
        $byMonth = $payrolls->groupBy(function($item) {
            return $item->period_start->format('Y-m');
        })->map(function($items, $month) {
            return [
                'month' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                'count' => $items->count(),
                'total_amount' => $items->sum('total_amount'),
                'workers' => $items->sum(function($item) {
                    return $item->details->count();
                }),
            ];
        })->sortKeys();

        // Top workers by payment
        $topWorkers = DB::table('payroll_request_details')
            ->join('workers', 'payroll_request_details.worker_id', '=', 'workers.worker_id')
            ->join('payroll_requests', 'payroll_request_details.payroll_request_id', '=', 'payroll_requests.payroll_request_id')
            ->select(
                'workers.worker_id',
                'workers.full_name',
                DB::raw('COUNT(payroll_request_details.detail_id) as total_payrolls'),
                DB::raw('SUM(payroll_request_details.days_worked) as total_days'),
                DB::raw('SUM(payroll_request_details.net_payment) as total_earned')
            )
            ->when($request->filled('start_date'), function($q) use ($request) {
                $q->where('payroll_requests.period_start', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function($q) use ($request) {
                $q->where('payroll_requests.period_end', '<=', $request->end_date);
            })
            ->groupBy('workers.worker_id', 'workers.full_name')
            ->orderByDesc('total_earned')
            ->limit(10)
            ->get();

        // Data for filters
        $activities = Activity::with('location')
            ->where(function($query) {
                $query->where('status', 'ongoing')
                      ->orWhere('status', 'completed');
            })
            ->orderBy('start_date', 'desc')
            ->get();

        $requesters = \App\Models\User::whereHas('payrollRequests')->get();

        return view('reports.payroll.index', compact(
            'payrolls',
            'summary',
            'byMonth',
            'topWorkers',
            'activities',
            'requesters'
        ));
    }

    public function export(Request $request)
    {
        $query = PayrollRequest::with('requester', 'approver', 'details.worker', 'activity');

        // Apply same filters
        if ($request->filled('start_date')) {
            $query->where('period_start', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('period_end', '<=', $request->end_date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        $payrolls = $query->latest('request_date')->get();

        $summary = [
            'total_requests' => $payrolls->count(),
            'total_amount' => $payrolls->sum('total_amount'),
            'total_workers' => $payrolls->sum(function($payroll) {
                return $payroll->details->count();
            }),
        ];

        $pdf = Pdf::loadView('reports.payroll.export', compact('payrolls', 'summary'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        $fileName = 'Laporan_Penggajian_' . Carbon::now()->format('YmdHis') . '.pdf';

        return $pdf->download($fileName);
    }

    public function workerSummary(Request $request)
    {
        $query = Worker::with(['payrollDetails.payrollRequest'])
            ->where('is_active', true);

        if ($request->filled('worker_id')) {
            $query->where('worker_id', $request->worker_id);
        }

        $workers = $query->get()->map(function($worker) use ($request) {
            $details = $worker->payrollDetails()
                ->whereHas('payrollRequest', function($q) use ($request) {
                    if ($request->filled('start_date')) {
                        $q->where('period_start', '>=', $request->start_date);
                    }
                    if ($request->filled('end_date')) {
                        $q->where('period_end', '<=', $request->end_date);
                    }
                })
                ->with('payrollRequest')
                ->get();

            return [
                'worker' => $worker,
                'total_payrolls' => $details->count(),
                'total_days' => $details->sum('days_worked'),
                'total_wage' => $details->sum('total_wage'),
                'total_bonus' => $details->sum('bonus'),
                'total_deduction' => $details->sum('deduction'),
                'total_earned' => $details->sum('net_payment'),
                'details' => $details,
            ];
        })->filter(function($item) {
            return $item['total_payrolls'] > 0;
        });

        $allWorkers = Worker::where('is_active', true)->get();

        return view('reports.payroll.worker-summary', compact('workers', 'allWorkers'));
    }
}