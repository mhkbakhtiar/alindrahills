<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Besar - {{ $perkiraan->nama_perkiraan }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; padding: 20px; }
        
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { font-size: 16px; font-weight: bold; }
        .header h2 { font-size: 13px; margin-top: 4px; }
        .header p { font-size: 10px; color: #666; margin-top: 2px; }

        .info-box { display: table; width: 100%; margin-bottom: 15px; }
        .info-row { display: table-row; }
        .info-cell { display: table-cell; padding: 3px 8px 3px 0; width: 25%; }
        .info-label { color: #666; font-size: 9px; }
        .info-value { font-weight: bold; font-size: 11px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead th { 
            background-color: #374151; 
            color: white; 
            padding: 6px 8px; 
            text-align: left; 
            font-size: 9px;
            font-weight: bold;
        }
        thead th.text-right { text-align: right; }
        tbody tr:nth-child(even) { background-color: #f9fafb; }
        tbody td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        tbody td.text-right { text-align: right; }
        
        .total-row { background-color: #e5e7eb !important; font-weight: bold; }
        .total-row td { border-top: 2px solid #374151; padding: 6px 8px; }

        .debet { color: #16a34a; }
        .kredit { color: #dc2626; }
        .dash { color: #9ca3af; }

        .footer { margin-top: 20px; text-align: right; font-size: 9px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BUKU BESAR (GENERAL LEDGER)</h1>
        <h2>{{ $perkiraan->kode_perkiraan }} - {{ $perkiraan->nama_perkiraan }}</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
    </div>

    <div class="info-box">
        <div class="info-row">
            <div class="info-cell">
                <div class="info-label">Kode Perkiraan</div>
                <div class="info-value">{{ $perkiraan->kode_perkiraan }}</div>
            </div>
            <div class="info-cell">
                <div class="info-label">Nama Perkiraan</div>
                <div class="info-value">{{ $perkiraan->nama_perkiraan }}</div>
            </div>
            <div class="info-cell">
                <div class="info-label">Jenis Akun</div>
                <div class="info-value">{{ $perkiraan->jenis_akun }}</div>
            </div>
            <div class="info-cell">
                <div class="info-label">Kategori</div>
                <div class="info-value">{{ $perkiraan->kategori ?? '-' }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Tanggal</th>
                <th style="width: 100px;">No. Bukti</th>
                <th>Keterangan</th>
                <th class="text-right" style="width: 110px;">Debet (Rp)</th>
                <th class="text-right" style="width: 110px;">Kredit (Rp)</th>
                <th class="text-right" style="width: 110px;">Saldo (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDebet = 0; $totalKredit = 0; @endphp
            @forelse($items as $item)
                @php
                    $totalDebet += $item->debet;
                    $totalKredit += $item->kredit;
                @endphp
                <tr>
                    <td>{{ $item->jurnal->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->jurnal->nomor_bukti }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                    <td class="text-right {{ $item->debet > 0 ? 'debet' : 'dash' }}">
                        {{ $item->debet > 0 ? number_format($item->debet, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right {{ $item->kredit > 0 ? 'kredit' : 'dash' }}">
                        {{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        {{ number_format($item->running_balance, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #6b7280;">
                        Tidak ada transaksi pada periode ini
                    </td>
                </tr>
            @endforelse

            @if($items->count() > 0)
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">TOTAL:</td>
                    <td class="text-right debet">{{ number_format($totalDebet, 0, ',', '.') }}</td>
                    <td class="text-right kredit">{{ number_format($totalKredit, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">
                        {{ number_format($totalDebet - $totalKredit, 0, ',', '.') }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>