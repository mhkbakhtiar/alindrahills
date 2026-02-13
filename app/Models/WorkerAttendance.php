<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerAttendance extends Model
{
    use HasFactory;

    protected $primaryKey = 'attendance_id';

    protected $fillable = [
        'worker_id',
        'attendance_date',
        'status',
        'check_in',
        'check_out',
        'hours_worked',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
        'hours_worked' => 'decimal:2',
    ];

    /**
     * Relasi ke Worker
     */
    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id', 'worker_id');
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    /**
     * Scope untuk filter berdasarkan bulan
     */
    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('attendance_date', $year)
                     ->whereMonth('attendance_date', $month);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Hitung jam kerja otomatis dari check in dan check out
     */
    public function calculateHoursWorked()
    {
        if ($this->check_in && $this->check_out) {
            $checkIn = \Carbon\Carbon::parse($this->check_in);
            $checkOut = \Carbon\Carbon::parse($this->check_out);
            $this->hours_worked = $checkOut->diffInHours($checkIn, true);
            $this->save();
        }
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return [
            'hadir' => 'success',
            'izin' => 'warning',
            'sakit' => 'info',
            'alpha' => 'danger',
        ][$this->status] ?? 'secondary';
    }
}