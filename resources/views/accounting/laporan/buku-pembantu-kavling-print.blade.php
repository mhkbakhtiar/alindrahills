{{-- resources/views/accounting/laporan/buku-pembantu-kavling-print.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Pembantu Kavling - {{ $kavlingPembeli->kavling->kavling ?? '-' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #333; padding: 15px; }
        .header { text-align: center; margin-bottom: 12px; border-bottom: 2px solid #333; padding-bottom: 8px; }
        .header h1 { font-size: 14px; font-weight: bold; }
        .header p { font-size: 9px; color: #555; margin-top: 2px; }
        .info-section { display: table; width: 100%; margin-bottom: 10px; border: 1px solid #e5e7eb; border-radius: 4px; padding: 8px; }
        .info-row { display: table-row; }
        .info-cell { display: table-cell; width: 25%; padding: 3px 5px; }
        .info-label { color: #777; font-size: 8px; }
        .info-value { font-weight: bold; font-size: 10px; }
        .summary { display: table; width: 100%; margin-bottom: 10px; }
        .summary-cell { display: table-cell; width: 25%; padding: 5px; background: #f3f4f6; border: 1px solid #e5e7eb; text-align: center; }
        .summary-label { font-size: 8px; color: #6b7280; }
        .summary-value { font-size: 11px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background-color: #1f2937; color: white; padding: 5px 6px; font-size: 8px; }
        thead th.text-right { text-align: right; }
        tbody td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; font-size: 8px; }
        tbody td.text-right { text-align: right; }
        .total-row td { background-color: #1f2937; color: white; font-weight: bold; padding: 5px 6px; }
        .total-row td.text-right { text-align: right; }
        .debet { color: #16a34a; }
        .kredit { color: #dc2626; }
        .footer { margin-top: 12px; text-align: right; font-size: 8px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BUKU PEMBANTU PER KAVLING</h1>
        <p>Kavling: {{ $kavlingPembeli->kavling->kavling }} - {{ $kavlingPembeli->kavling->blok ?? '' }}</p>
        <p>Periode: {{ \Carbon\Carbon::parse($dari)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->format('d F Y') }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-cell"><div class="info-label">Kavling</div><div class="info-value">{{ $kavlingPembeli->kavling->kavling ?? '-' }}</div></div>
            <div class="info-cell"><div class="info-label">Blok</div><div class="info-value">{{ $kavlingPembeli->kavling->blok ?? '-' }}</div></div>
            <div class="info-cell"><div class="info-label">Pembeli</div><div class="info-value">{{ $kavlingPembeli->pembeli->nama ?? '-' }}</div></div>
            <div class="info-cell"><div class="info-label">Harga Jual</div><div class="info-value">Rp {{ number_format($kavlingPembeli->harga_jual ?? 0, 0, ',', '.') }}</div></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:65px">Tanggal</th>
                <th style="width:90px">No. Bukti</th>
                <th style="width:80px">Kode</th>
                <th>Perkiraan</th>
                <th>Keterangan</th>
                <th class="text-right" style="width:90px">Debet (Rp)</th>
                <th class="text-right" style="width:90px">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $item)
                <tr>
                    <td>{{ $item->jurnal->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->jurnal->nomor_bukti }}</td>
                    <td>{{ $item->kode_perkiraan }}</td>
                    <td>{{ $item->perkiraan->nama_perkiraan ?? '-' }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                    <td class="text-right debet">{{ $item->debet > 0 ? number_format($item->debet, 0, ',', '.') : '-' }}</td>
                    <td class="text-right kredit">{{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:15px">Tidak ada transaksi</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align:right">TOTAL:</td>
                <td class="text-right">{{ number_format($totalDebet, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalKredit, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">Dicetak: {{ now()->format('d/m/Y H:i:s') }}</div>
</body>
</html>