<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Gudang</title>
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
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>LAPORAN STOK GUDANG</h1>
    <h2>Per {{ now()->format('d F Y') }}</h2>

    <!-- Summary Section -->
    <div class="summary-box">
        <strong>Ringkasan:</strong><br>
        <div class="summary-item">Total Item: <strong>{{ $summary['total_items'] }}</strong></div>
        <div class="summary-item">Total Nilai: <strong>Rp {{ number_format($summary['total_value'], 0) }}</strong></div>
        <div class="summary-item">Total Qty: <strong>{{ number_format($summary['total_quantity'], 2) }}</strong></div>
    </div>

    <!-- Stock List -->
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Kode</th>
                <th width="20%">Nama Material</th>
                <th width="12%">Gudang</th>
                <th width="10%">Kategori</th>
                <th width="10%" class="text-center">Stok</th>
                <th width="8%" class="text-center">Min</th>
                <th width="12%" class="text-right">Harga Avg</th>
                <th width="13%" class="text-right">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $index => $stock)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $stock->material->material_code }}</td>
                    <td>{{ $stock->material->material_name }}</td>
                    <td>{{ $stock->warehouse->warehouse_name }}</td>
                    <td>{{ $stock->material->category }}</td>
                    <td class="text-center">{{ number_format($stock->current_stock, 2) }} {{ $stock->material->unit }}</td>
                    <td class="text-center">{{ number_format($stock->material->min_stock, 2) }}</td>
                    <td class="text-right">Rp {{ number_format($stock->average_price, 0) }}</td>
                    <td class="text-right">Rp {{ number_format($stock->current_stock * $stock->average_price, 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
            @if($stocks->count() > 0)
                <tr>
                    <td colspan="8" class="text-right"><strong>TOTAL:</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($summary['total_value'], 0) }}</strong></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d F Y H:i:s') }}
    </div>
</body>
</html>