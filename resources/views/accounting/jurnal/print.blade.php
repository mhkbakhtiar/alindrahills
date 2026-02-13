{{-- resources/views/accounting/jurnal/print.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Jurnal - {{ $jurnal->nomor_bukti }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 150px;
            padding: 5px 0;
            font-weight: bold;
        }
        
        .info-value {
            display: table-cell;
            padding: 5px 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .items-table td.right {
            text-align: right;
        }
        
        .items-table td.center {
            text-align: center;
        }
        
        .items-table tfoot td {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        
        .debet {
            color: #059669;
        }
        
        .kredit {
            color: #dc2626;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .status-draft {
            background-color: #e5e7eb;
            color: #374151;
        }
        
        .status-posted {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-void {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .footer {
            margin-top: 40px;
            text-align: right;
        }
        
        .signature-section {
            display: inline-block;
            text-align: center;
            margin-left: 50px;
        }
        
        .signature-line {
            width: 200px;
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer;">
            🖨️ Print
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 5px;">
            ✖️ Tutup
        </button>
    </div>

    <div class="header">
        <h1>VOUCHER JURNAL UMUM</h1>
        <h2>{{ config('app.name', 'Perusahaan') }}</h2>
    </div>
    
    <div class="info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nomor Bukti:</div>
                <div class="info-value">{{ $jurnal->nomor_bukti }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jenis Jurnal:</div>
                <div class="info-value">{{ ucfirst($jurnal->jenis_jurnal) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $jurnal->status }}">
                        {{ strtoupper($jurnal->status) }}
                    </span>
                </div>
            </div>
            @if($jurnal->departemen)
            <div class="info-row">
                <div class="info-label">Departemen:</div>
                <div class="info-value">{{ $jurnal->departemen }}</div>
            </div>
            @endif
            @if($jurnal->keterangan)
            <div class="info-row">
                <div class="info-label">Keterangan:</div>
                <div class="info-value">{{ $jurnal->keterangan }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Dibuat Oleh:</div>
                <div class="info-value">{{ $jurnal->creator->full_name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Dibuat:</div>
                <div class="info-value">{{ $jurnal->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
    
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30px;" class="center">#</th>
                <th style="width: 100px;">Kode</th>
                <th>Nama Perkiraan</th>
                <th>Keterangan</th>
                <th style="width: 120px;" class="right">Debet (Rp)</th>
                <th style="width: 120px;" class="right">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDebet = 0;
                $totalKredit = 0;
            @endphp
            @foreach($jurnal->items as $index => $item)
                @php
                    $totalDebet += $item->debet;
                    $totalKredit += $item->kredit;
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->kode_perkiraan }}</td>
                    <td>{{ $item->perkiraan->nama_perkiraan ?? '-' }}</td>
                    <td>
                        {{ $item->keterangan ?? '-' }}
                        @if($item->kode_kavling)
                            <br><small style="color: #666;">Kavling: {{ $item->kode_kavling }}</small>
                        @endif
                        @if($item->user)
                            <br><small style="color: #666;">User: {{ $item->user->full_name }}</small>
                        @endif
                    </td>
                    <td class="right">
                        @if($item->debet > 0)
                            <span class="debet">{{ number_format($item->debet, 0, ',', '.') }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="right">
                        @if($item->kredit > 0)
                            <span class="kredit">{{ number_format($item->kredit, 0, ',', '.') }}</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="right">TOTAL:</td>
                <td class="right debet">{{ number_format($totalDebet, 0, ',', '.') }}</td>
                <td class="right kredit">{{ number_format($totalKredit, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="6" class="center">
                    @if(abs($totalDebet - $totalKredit) < 0.01)
                        <span style="color: #059669;">✓ BALANCE</span>
                    @else
                        <span style="color: #dc2626;">✗ TIDAK BALANCE (Selisih: Rp {{ number_format(abs($totalDebet - $totalKredit), 0, ',', '.') }})</span>
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <div class="signature-section">
            <div>Dibuat Oleh,</div>
            <div class="signature-line">{{ $jurnal->creator->full_name ?? '_______________' }}</div>
        </div>
        
        <div class="signature-section">
            <div>Diperiksa Oleh,</div>
            <div class="signature-line">_______________</div>
        </div>
        
        <div class="signature-section">
            <div>Disetujui Oleh,</div>
            <div class="signature-line">_______________</div>
        </div>
    </div>
    
    <div style="margin-top: 40px; text-align: center; color: #999; font-size: 10px;">
        Dicetak pada: {{ now()->format('d F Y H:i:s') }}
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>