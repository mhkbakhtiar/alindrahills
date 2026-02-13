<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penggajian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 5px;
        }
        h2 {
            text-align: center;
            font-size: 14px;
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: normal;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .summary-box {
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        .summary-item {
            display: inline-block;
            width: 32%;
            margin-right: 1%;
        }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>LAPORAN PENGGAJIAN TUKANG</h1>
    <h2>Per {{ now()->format('d F Y') }}</h2>

    <!-- Summary Section -->
    <div class="summary-box">
        <strong>Ringkasan:</strong><br>
        <div class="summary-item">Total Pengajuan: <strong>{{ $summary['total_requests'] }}</strong></div>
        <div class="summary-item">Total Nominal: <strong>Rp {{ number_format($summary['total_amount'], 0) }}</strong></div>
        <div class="summary-item">Total Tukang: <strong>{{ $summary['total_workers'] }}</strong></div>
    </div>

    <!-- Payroll List -->
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">No. Pengajuan</th>
                <th width="12%">Tanggal</th>
                <th width="18%">Periode</th>
                <th width="15%">Kegiatan</th>
                <th width="10%" class="text-center">Jml Tukang</th>
                <th width="15%" class="text-right">Total</th>
                <th width="10%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payrolls as $index => $payroll)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $payroll->request_number }}</td>
                    <td>{{ $payroll->request_date->format('d M Y') }}</td>
                    <td>{{ $payroll->period_start->format('d M') }} - {{ $payroll->period_end->format('d M Y') }}</td>
                    <td>{{ $payroll->activity?->activity_code ?? '-' }}</td>
                    <td class="text-center">{{ $payroll->details->count() }}</td>
                    <td class="text-right">Rp {{ number_format($payroll->total_amount, 0) }}</td>
                    <td class="text-center">{{ ucfirst($payroll->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
            @if($payrolls->count() > 0)
                <tr>
                    <td colspan="6" class="text-right"><strong>TOTAL:</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($payrolls->sum('total_amount'), 0) }}</strong></td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d F Y H:i:s') }}
    </div>
</body>
</html>