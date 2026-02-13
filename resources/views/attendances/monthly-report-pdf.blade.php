<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Bulanan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 16pt;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10pt;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 15px;
            background: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            width: 100px;
        }
        
        .summary-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        
        .summary-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            width: 25%;
        }
        
        .summary-table .label {
            font-size: 8pt;
            color: #666;
            display: block;
            margin-bottom: 5px;
        }
        
        .summary-table .value {
            font-size: 16pt;
            font-weight: bold;
            display: block;
        }
        
        .summary-table .value.green { color: #10b981; }
        .summary-table .value.yellow { color: #f59e0b; }
        .summary-table .value.red { color: #ef4444; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background-color: #f3f4f6;
        }
        
        table th {
            padding: 8px 5px;
            text-align: left;
            font-size: 8pt;
            font-weight: bold;
            border: 1px solid #d1d5db;
        }
        
        table td {
            padding: 6px 5px;
            font-size: 8pt;
            border: 1px solid #e5e7eb;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        table tbody tr:hover {
            background-color: #f3f4f6;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        
        .font-medium { font-weight: 600; }
        .font-semibold { font-weight: 700; }
        
        .bg-green-50 { background-color: #f0fdf4 !important; }
        .bg-yellow-50 { background-color: #fffbeb !important; }
        .bg-blue-50 { background-color: #eff6ff !important; }
        .bg-red-50 { background-color: #fef2f2 !important; }
        
        .bg-green-100 { background-color: #dcfce7 !important; }
        .bg-yellow-100 { background-color: #fef3c7 !important; }
        .bg-blue-100 { background-color: #dbeafe !important; }
        .bg-red-100 { background-color: #fee2e2 !important; }
        
        .text-green-600 { color: #16a34a; }
        
        .total-row {
            background-color: #e5e7eb !important;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 7pt;
            color: #666;
            text-align: center;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN ABSENSI BULANAN TUKANG</h1>
        <p>Periode: {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Tanggal Cetak:</span>
            <span>{{ date('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Tukang:</span>
            <span>{{ count($report) }} orang</span>
        </div>
    </div>

    <table class="summary-table">
        <tr>
            <td>
                <span class="label">Total Tukang</span>
                <span class="value">{{ count($report) }}</span>
            </td>
            <td>
                <span class="label">Total Kehadiran</span>
                <span class="value green">{{ collect($report)->sum('hadir') }}</span>
            </td>
            <td>
                <span class="label">Total Izin/Sakit</span>
                <span class="value yellow">{{ collect($report)->sum('izin') + collect($report)->sum('sakit') }}</span>
            </td>
            <td>
                <span class="label">Total Alpha</span>
                <span class="value red">{{ collect($report)->sum('alpha') }}</span>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 8%;">Kode</th>
                <th style="width: 15%;">Nama Tukang</th>
                <th style="width: 10%;">Jenis</th>
                <th class="text-center" style="width: 6%;">Total Hari</th>
                <th class="text-center bg-green-50" style="width: 5%;">Hadir</th>
                <th class="text-center bg-yellow-50" style="width: 5%;">Izin</th>
                <th class="text-center bg-blue-50" style="width: 5%;">Sakit</th>
                <th class="text-center bg-red-50" style="width: 5%;">Alpha</th>
                <th class="text-right" style="width: 8%;">Total Jam</th>
                <th class="text-right" style="width: 12%;">Upah Harian</th>
                <th class="text-right" style="width: 13%;">Total Upah</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($report as $item)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="font-medium">{{ $item['worker']->worker_code }}</td>
                    <td>{{ $item['worker']->full_name }}</td>
                    <td>{{ $item['worker']->worker_type }}</td>
                    <td class="text-center font-medium">{{ $item['total_days'] }}</td>
                    <td class="text-center bg-green-50">{{ $item['hadir'] }}</td>
                    <td class="text-center bg-yellow-50">{{ $item['izin'] }}</td>
                    <td class="text-center bg-blue-50">{{ $item['sakit'] }}</td>
                    <td class="text-center bg-red-50">{{ $item['alpha'] }}</td>
                    <td class="text-right">{{ number_format($item['total_hours'], 1) }}</td>
                    <td class="text-right">Rp {{ number_format($item['worker']->daily_rate, 0, ',', '.') }}</td>
                    <td class="text-right font-medium text-green-600">
                        Rp {{ number_format($item['hadir'] * $item['worker']->daily_rate, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 30px;">Tidak ada data untuk periode ini</td>
                </tr>
            @endforelse
            
            @if(count($report) > 0)
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL:</td>
                <td class="text-center">{{ collect($report)->sum('total_days') }}</td>
                <td class="text-center bg-green-100">{{ collect($report)->sum('hadir') }}</td>
                <td class="text-center bg-yellow-100">{{ collect($report)->sum('izin') }}</td>
                <td class="text-center bg-blue-100">{{ collect($report)->sum('sakit') }}</td>
                <td class="text-center bg-red-100">{{ collect($report)->sum('alpha') }}</td>
                <td class="text-right">{{ number_format(collect($report)->sum('total_hours'), 1) }}</td>
                <td class="text-right">-</td>
                <td class="text-right text-green-600">
                    Rp {{ number_format(collect($report)->sum(function($item) {
                        return $item['hadir'] * $item['worker']->daily_rate;
                    }), 0, ',', '.') }}
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem pada {{ date('d/m/Y H:i:s') }}</p>
        <p>Laporan Absensi Bulanan - {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</p>
    </div>
</body>
</html>