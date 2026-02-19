{{-- resources/views/accounting/laporan/neraca-print.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Neraca</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #333; padding: 15px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px; }
        .header h1 { font-size: 14px; font-weight: bold; }
        .header p { font-size: 9px; color: #555; margin-top: 2px; }
        .two-col { display: table; width: 100%; }
        .col { display: table-cell; width: 50%; vertical-align: top; padding: 0 5px; }
        .col:first-child { padding-left: 0; }
        .col:last-child { padding-right: 0; }
        .section-title { font-size: 10px; font-weight: bold; padding: 5px 0; border-bottom: 1px solid #333; margin-bottom: 5px; }
        .aset-title { color: #1e40af; }
        .kewajiban-title { color: #991b1b; }
        .modal-title { color: #166534; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        tbody td { padding: 3px 4px; border-bottom: 1px solid #f3f4f6; font-size: 8px; }
        tbody td.text-right { text-align: right; }
        .subtotal-row td { font-weight: bold; border-top: 2px solid #333; padding: 4px; }
        .subtotal-row td.text-right { text-align: right; }
        .grand-total { margin-top: 15px; border-top: 2px solid #333; padding-top: 8px; }
        .grand-total table td { font-weight: bold; font-size: 10px; padding: 4px; }
        .footer { margin-top: 15px; text-align: right; font-size: 8px; color: #6b7280; }
        .balance-status { text-align: center; margin-top: 10px; padding: 5px; border-radius: 4px; font-weight: bold; font-size: 10px; }
        .balance-ok { background: #dcfce7; color: #166534; }
        .balance-not { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>NERACA (BALANCE SHEET)</h1>
        <p>Per Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</p>
    </div>

    <div class="two-col">
        {{-- ASET --}}
        <div class="col">
            <div class="section-title aset-title">ASET</div>
            <table>
                <tbody>
                    @foreach($aset as $item)
                        <tr>
                            <td style="width:60px">{{ $item->kode_perkiraan }}</td>
                            <td>{{ $item->nama_perkiraan }}</td>
                            <td class="text-right" style="width:90px">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="subtotal-row">
                        <td colspan="2">TOTAL ASET</td>
                        <td class="text-right">{{ number_format($totalAset, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- KEWAJIBAN + MODAL --}}
        <div class="col">
            <div class="section-title kewajiban-title">KEWAJIBAN</div>
            <table>
                <tbody>
                    @foreach($kewajiban as $item)
                        <tr>
                            <td style="width:60px">{{ $item->kode_perkiraan }}</td>
                            <td>{{ $item->nama_perkiraan }}</td>
                            <td class="text-right" style="width:90px">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="subtotal-row">
                        <td colspan="2">TOTAL KEWAJIBAN</td>
                        <td class="text-right">{{ number_format($totalKewajiban, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="section-title modal-title" style="margin-top:10px">MODAL</div>
            <table>
                <tbody>
                    @foreach($modal as $item)
                        <tr>
                            <td style="width:60px">{{ $item->kode_perkiraan }}</td>
                            <td>{{ $item->nama_perkiraan }}</td>
                            <td class="text-right" style="width:90px">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>-</td>
                        <td><em>Laba/Rugi Berjalan</em></td>
                        <td class="text-right">{{ number_format($labaRugi, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="subtotal-row">
                        <td colspan="2">TOTAL MODAL</td>
                        <td class="text-right">{{ number_format($totalModal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            <table style="margin-top:5px">
                <tfoot>
                    <tr class="subtotal-row">
                        <td colspan="2">TOTAL KEWAJIBAN + MODAL</td>
                        <td class="text-right" style="width:90px">{{ number_format($totalKewajiban + $totalModal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="balance-status {{ abs($totalAset - ($totalKewajiban + $totalModal)) < 1 ? 'balance-ok' : 'balance-not' }}">
        {{ abs($totalAset - ($totalKewajiban + $totalModal)) < 1 ? '✓ NERACA BALANCE' : '✗ NERACA TIDAK BALANCE (Selisih: Rp ' . number_format(abs($totalAset - ($totalKewajiban + $totalModal)), 0, ',', '.') . ')' }}
    </div>

    <div class="footer">Dicetak: {{ now()->format('d/m/Y H:i:s') }}</div>
</body>
</html>