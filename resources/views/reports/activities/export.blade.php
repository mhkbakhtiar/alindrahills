<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kegiatan</title>
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
            width: 23%;
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
    <h1>LAPORAN KEGIATAN PROYEK</h1>
    <h2>Per {{ now()->format('d F Y') }}</h2>

    <!-- Summary Section -->
    <div class="summary-box">
        <strong>Ringkasan:</strong><br>
        <div class="summary-item">Total Kegiatan: <strong>{{ $summary['total_activities'] }}</strong></div>
        <div class="summary-item">Planned: <strong>{{ $summary['planned'] }}</strong></div>
        <div class="summary-item">Ongoing: <strong>{{ $summary['ongoing'] }}</strong></div>
        <div class="summary-item">Completed: <strong>{{ $summary['completed'] }}</strong></div>
    </div>

    <!-- Activity List -->
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Kode</th>
                <th width="20%">Nama Kegiatan</th>
                <th width="15%">Lokasi</th>
                <th width="12%">Tipe</th>
                <th width="18%">Periode</th>
                <th width="8%" class="text-center">Tukang</th>
                <th width="10%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activities as $index => $activity)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $activity->activity_code }}</td>
                    <td>{{ $activity->activity_name }}</td>
                    <td>{{ $activity->location->location_name ?? '-' }}</td>
                    <td>{{ $activity->activity_type }}</td>
                    <td>{{ $activity->start_date->format('d M Y') }} - {{ $activity->end_date?->format('d M Y') ?? 'N/A' }}</td>
                    <td class="text-center">{{ $activity->activityWorkers->count() }}</td>
                    <td class="text-center">{{ ucfirst($activity->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d F Y H:i:s') }}
    </div>
</body>
</html>