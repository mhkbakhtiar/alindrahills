{{-- resources/views/accounting/laporan/laba-rugi-print.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Laba Rugi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #333; padding: 15px; max-width: 600px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px; }
        .header h1 { font-size: 14px; font-weight: bold; }
        .header p { font-size: 9px; color: #555; margin-top: 2px; }
        .section-title { font-size: 10px; font-weight: bold; padding: 6px 0; border-bottom: 1px solid #333; margin: 10px 0 5px; }
        .pendapatan-title { color: #166534; }
        .biaya-title { color: #991b1b; }
        table { width: 100%; border-collapse: collapse; }
        tbody td { padding: 3px 4px; border-bottom: 1px solid #f3f4f6; font-size: 8px; }
        tbody td.text-right { text-align: right; }
        .subtotal-row td { font-weight: bold; border-top: 1px solid #333; padding: 5px 4px; }
        .subtotal-row td.text-right { text-align: right; }
        .laba-rugi-box { margin-top: 15px; padding: 10px; border: 2px solid #333; text-align: center; }
        .laba-rugi-box .label { font-size: 10px; font-weight: bold; }
        .laba-rugi-box .amount { font-size: 16px; font-weight: bold; margin-top: 3px; }
        .laba { color: #166534; }
        .rugi { color: #991b1b; }
        .footer { margin-top: 15px; text-align: right; font-size: 8px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN LABA RUGI</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($dari)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->format('d F Y') }}</p>
    </div>

    <div class="section-title pendapatan-title">PENDAPATAN</div>
    <table>
        <tbody>
            @foreach($pendapatan as $item)
                <tr>
                    <td style="width:65px">{{ $item->kode_perkiraan }}</td>
                    <td>{{ $item->nama_perkiraan }}</td>
                    <td class="text-right" style="width:100px">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="subtotal-row">
                <td colspan="2">TOTAL PENDAPATAN</td>
                <td class="text-right">{{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="section-title biaya-title">BIAYA</div>
    <table>
        <tbody>
            @foreach($biaya as $item)
                <tr>
                    <td style="width:65px">{{ $item->kode_perkiraan }}</td>
                    <td>{{ $item->nama_perkiraan }}</td>
                    <td class="text-right" style="width:100px">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="subtotal-row">
                <td colspan="2">TOTAL BIAYA</td>
                <td class="text-right">{{ number_format($totalBiaya, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="laba-rugi-box">
        <div class="label {{ $labaRugi >= 0 ? 'laba' : 'rugi' }}">{{ $labaRugi >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}</div>
        <div class="amount {{ $labaRugi >= 0 ? 'laba' : 'rugi' }}">Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}</div>
        <div style="font-size:8px;color:#6b7280;margin-top:3px">(Pendapatan - Biaya)</div>
    </div>

    <div class="footer">Dicetak: {{ now()->format('d/m/Y H:i:s') }}</div>
</body>
</html>