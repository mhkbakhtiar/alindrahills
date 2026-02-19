{{-- resources/views/accounting/laporan/buku-besar-print.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Besar - {{ $akun->kode_perkiraan }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #333; padding: 15px; }
        .header { text-align: center; margin-bottom: 12px; border-bottom: 2px solid #333; padding-bottom: 8px; }
        .header h1 { font-size: 14px; font-weight: bold; }
        .header h2 { font-size: 11px; margin-top: 3px; }
        .header p { font-size: 9px; color: #555; margin-top: 2px; }
        .info-grid { display: table; width: 100%; margin-bottom: 10px; }
        .info-row { display: table-row; }
        .info-cell { display: table-cell; padding: 2px 10px 2px 0; }
        .info-label { color: #777; font-size: 8px; }
        .info-value { font-weight: bold; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background-color: #1f2937; color: white; padding: 5px 6px; font-size: 8px; }
        thead th.text-right { text-align: right; }
        tbody td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; font-size: 8px; }
        tbody td.text-right { text-align: right; }
        .saldo-awal { background-color: #eff6ff; font-weight: bold; }
        .total-row { background-color: #1f2937; color: white; font-weight: bold; }
        .total-row td { padding: 5px 6px; }
        .total-row td.text-right { text-align: right; }
        .debet { color: #16a34a; }
        .kredit { color: #dc2626; }
        .footer { margin-top: 12px; text-align: right; font-size: 8px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BUKU BESAR (GENERAL LEDGER)</h1>
        <h2>{{ $akun->kode_perkiraan }} - {{ $akun->nama_perkiraan }}</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($dari)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:65px">Tanggal</th>
                <th style="width:100px">No. Bukti</th>
                <th>Keterangan</th>
                <th class="text-right" style="width:90px">Debet (Rp)</th>
                <th class="text-right" style="width:90px">Kredit (Rp)</th>
                <th class="text-right" style="width:90px">Saldo (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="saldo-awal">
                <td colspan="5">Saldo Awal</td>
                <td class="text-right">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
            </tr>
            @php $saldo = $saldoAwal; $totalDebet = 0; $totalKredit = 0; @endphp
            @forelse($mutasi as $item)
                @php $saldo += ($item->debet - $item->kredit); $totalDebet += $item->debet; $totalKredit += $item->kredit; @endphp
                <tr>
                    <td>{{ $item->jurnal->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->jurnal->nomor_bukti }}</td>
                    <td>{{ $item->keterangan ?? $item->jurnal->keterangan ?? '-' }}</td>
                    <td class="text-right debet">{{ $item->debet > 0 ? number_format($item->debet, 0, ',', '.') : '-' }}</td>
                    <td class="text-right kredit">{{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '-' }}</td>
                    <td class="text-right" style="font-weight:bold">{{ number_format($saldo, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;padding:15px">Tidak ada mutasi</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" style="text-align:right">SALDO AKHIR:</td>
                <td class="text-right">{{ number_format($totalDebet, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalKredit, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($saldo, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">Dicetak: {{ now()->format('d/m/Y H:i:s') }}</div>
</body>
</html>