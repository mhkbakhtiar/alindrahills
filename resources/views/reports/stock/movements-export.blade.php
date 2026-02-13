<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Mutasi Stok</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
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
            padding: 5px;
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
            font-size: 8px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>LAPORAN MUTASI STOK</h1>
    <h2>Per {{ now()->format('d F Y') }}</h2>

    <!-- Summary Section -->
    <div class="summary-box">
        <strong>Ringkasan:</strong><br>
        <div class="summary-item">Total Mutasi: <strong>{{ $summary['total_movements'] }}</strong></div>
        <div class="summary-item">Total Masuk: <strong>{{ number_format($summary['total_in'], 2) }}</strong></div>
        <div class="summary-item">Total Keluar: <strong>{{ number_format($summary['total_out'], 2) }}</strong></div>
    </div>

    <!-- Mutations List -->
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Tanggal</th>
                <th width="25%">Material</th>
                <th width="12%">Gudang</th>
                <th width="8%" class="text-center">Tipe</th>
                <th width="12%" class="text-right">Qty</th>
                <th width="13%" class="text-right">Harga</th>
                <th width="15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mutations as $index => $mutation)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $mutation->created_at->format('d M Y') }}</td>
                    <td>{{ $mutation->material->material_code }} - {{ $mutation->material->material_name }}</td>
                    <td>{{ $mutation->warehouse->warehouse_name }}</td>
                    <td class="text-center">{{ $mutation->mutation_type == 'in' ? 'Masuk' : 'Keluar' }}</td>
                    <td class="text-right">{{ number_format($mutation->qty, 2) }} {{ $mutation->material->unit }}</td>
                    <td class="text-right">Rp {{ number_format($mutation->unit_price, 0) }}</td>
                    <td class="text-right">Rp {{ number_format($mutation->total_value, 0) }}</td>
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