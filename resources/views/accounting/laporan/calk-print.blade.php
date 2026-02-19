{{-- resources/views/accounting/laporan/calk-print.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CALK</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #333; padding: 15px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px; }
        .header h1 { font-size: 14px; font-weight: bold; }
        .header h2 { font-size: 11px; margin-top: 3px; }
        .header p { font-size: 9px; color: #555; margin-top: 2px; }
        .section { margin-bottom: 15px; }
        .section-title { font-size: 10px; font-weight: bold; padding: 5px 6px; background: #1f2937; color: white; margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; }
        tbody td { padding: 3px 6px; border-bottom: 1px solid #f3f4f6; font-size: 8px; }
        tbody td.text-right { text-align: right; }
        .subtotal-row td { font-weight: bold; border-top: 1px solid #374151; background: #f3f4f6; padding: 4px 6px; }
        .subtotal-row td.text-right { text-align: right; }
        .two-col { display: table; width: 100%; border-spacing: 10px; }
        .col { display: table-cell; width: 50%; vertical-align: top; padding-right: 10px; }
        .col:last-child { padding-right: 0; padding-left: 10px; }
        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CATATAN ATAS LAPORAN KEUANGAN (CALK)</h1>
        <p>Per Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</p>
    </div>

    <div class="two-col">
        <div class="col">
            @foreach(array_slice($data, 0, ceil(count($data)/2)) as $key => $section)
                <div class="section">
                    <div class="section-title">{{ $section['title'] }}</div>
                    <table>
                        <tbody>
                            @forelse($section['items'] as $item)
                                <tr>
                                    <td style="width:60px">{{ $item->kode_perkiraan }}</td>
                                    <td>{{ $item->nama_perkiraan }}</td>
                                    <td class="text-right" style="width:90px">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align:center;padding:8px;color:#9ca3af">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="subtotal-row">
                                <td colspan="2">TOTAL</td>
                                <td class="text-right">{{ number_format($section['total'], 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endforeach
        </div>

        <div class="col">
            @foreach(array_slice($data, ceil(count($data)/2)) as $key => $section)
                <div class="section">
                    <div class="section-title">{{ $section['title'] }}</div>
                    <table>
                        <tbody>
                            @forelse($section['items'] as $item)
                                <tr>
                                    <td style="width:60px">{{ $item->kode_perkiraan }}</td>
                                    <td>{{ $item->nama_perkiraan }}</td>
                                    <td class="text-right" style="width:90px">{{ number_format($item->saldo, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align:center;padding:8px;color:#9ca3af">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="subtotal-row">
                                <td colspan="2">TOTAL</td>
                                <td class="text-right">{{ number_format($section['total'], 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endforeach
        </div>
    </div>

    <div class="footer">Dicetak: {{ now()->format('d/m/Y H:i:s') }}</div>
</body>
</html>