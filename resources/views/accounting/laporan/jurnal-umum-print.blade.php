{{-- resources/views/accounting/laporan/jurnal-umum-print.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jurnal Umum</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #333; padding: 15px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 8px; }
        .header h1 { font-size: 14px; font-weight: bold; }
        .header p { font-size: 9px; color: #555; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead th { background-color: #1f2937; color: white; padding: 5px 6px; text-align: left; font-size: 8px; }
        thead th.text-right { text-align: right; }
        tbody tr:nth-child(even) { background-color: #f9fafb; }
        tbody td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; font-size: 8px; vertical-align: top; }
        tbody td.text-right { text-align: right; }
        .subtotal-row td { background-color: #f3f4f6; font-weight: bold; border-top: 1px solid #d1d5db; font-style: italic; }
        .total-row td { background-color: #1f2937; color: white; font-weight: bold; padding: 5px 6px; }
        .total-row td.text-right { text-align: right; }
        .debet { color: #16a34a; }
        .kredit { color: #dc2626; }
        .footer { margin-top: 15px; text-align: right; font-size: 8px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN JURNAL UMUM</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($dari)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->format('d F Y') }}</p>
    </div>

    @php $grandDebet = 0; $grandKredit = 0; @endphp

    <table>
        <thead>
            <tr>
                <th style="width:70px">Tanggal</th>
                <th style="width:100px">No. Bukti</th>
                <th style="width:60px">Kode</th>
                <th>Nama Perkiraan</th>
                <th>Keterangan</th>
                <th class="text-right" style="width:90px">Debet (Rp)</th>
                <th class="text-right" style="width:90px">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jurnal as $j)
                @php $jDebet = $j->items->sum('debet'); $jKredit = $j->items->sum('kredit'); $grandDebet += $jDebet; $grandKredit += $jKredit; @endphp
                @foreach($j->items as $index => $item)
                    <tr>
                        @if($index === 0)
                            <td rowspan="{{ $j->items->count() + 1 }}">{{ \Carbon\Carbon::parse($j->tanggal)->format('d/m/Y') }}</td>
                            <td rowspan="{{ $j->items->count() + 1 }}">{{ $j->nomor_bukti }}</td>
                        @endif
                        <td>{{ $item->kode_perkiraan }}</td>
                        <td>{{ $item->perkiraan->nama_perkiraan ?? '-' }}</td>
                        <td>{{ $item->keterangan ?? $j->keterangan ?? '-' }}</td>
                        <td class="text-right debet">{{ $item->debet > 0 ? number_format($item->debet, 0, ',', '.') : '-' }}</td>
                        <td class="text-right kredit">{{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '-' }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td colspan="3" style="text-align:right">Subtotal:</td>
                    <td class="text-right debet">{{ number_format($jDebet, 0, ',', '.') }}</td>
                    <td class="text-right kredit">{{ number_format($jKredit, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:15px">Tidak ada data</td></tr>
            @endforelse
        </tbody>
        @if($jurnal->count() > 0)
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align:right">GRAND TOTAL:</td>
                <td class="text-right">{{ number_format($grandDebet, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($grandKredit, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">Dicetak: {{ now()->format('d/m/Y H:i:s') }}</div>
</body>
</html>