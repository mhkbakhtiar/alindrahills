<?php

namespace App\Services;

use App\Models\WorkerAttendance;
use App\Models\PayrollRequest;
use App\Models\PayrollRequestDetail;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Calculate payroll for period
     */
    public function calculatePayroll($periodStart, $periodEnd)
    {
        $attendances = WorkerAttendance::whereBetween('attendance_date', [$periodStart, $periodEnd])
            ->where('status', 'present')
            ->with(['assignment.worker'])
            ->get()
            ->groupBy('assignment.worker_id');

        $payrollData = [];

        foreach ($attendances as $workerId => $records) {
            $worker = $records->first()->assignment->worker;
            $daysWorked = $records->sum(function($record) {
                return $record->status === 'half_day' ? 0.5 : 1;
            });

            $payrollData[] = [
                'worker_id' => $workerId,
                'worker_name' => $worker->full_name,
                'days_worked' => $daysWorked,
                'daily_rate' => $worker->daily_rate,
                'total_wage' => $daysWorked * $worker->daily_rate,
            ];
        }

        return $payrollData;
    }

    /**
     * Create payroll request
     */
    public function createPayrollRequest($data)
    {
        DB::beginTransaction();
        try {
            $payrollRequest = PayrollRequest::create([
                'request_number' => $this->generateRequestNumber(),
                'request_date' => now(),
                'period_start' => $data['period_start'],
                'period_end' => $data['period_end'],
                'requested_by' => auth()->id(),
                'letter_number' => $data['letter_number'] ?? null,
                'letter_date' => $data['letter_date'] ?? null,
                'status' => 'pending',
            ]);

            $totalAmount = 0;

            foreach ($data['workers'] as $worker) {
                $netPayment = $worker['total_wage'] + ($worker['bonus'] ?? 0) - ($worker['deduction'] ?? 0);
                
                PayrollRequestDetail::create([
                    'payroll_request_id' => $payrollRequest->payroll_request_id,
                    'worker_id' => $worker['worker_id'],
                    'days_worked' => $worker['days_worked'],
                    'daily_rate' => $worker['daily_rate'],
                    'total_wage' => $worker['total_wage'],
                    'bonus' => $worker['bonus'] ?? 0,
                    'deduction' => $worker['deduction'] ?? 0,
                    'net_payment' => $netPayment,
                    'notes' => $worker['notes'] ?? null,
                ]);

                $totalAmount += $netPayment;
            }

            $payrollRequest->update(['total_amount' => $totalAmount]);

            DB::commit();
            return $payrollRequest;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Generate request number
     */
    private function generateRequestNumber()
    {
        return 'PAY-' . date('Ymd') . '-' . str_pad(PayrollRequest::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }
}
