<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Purchase Request - {{ $purchaseRequest->request_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #7c3aed;
            padding-bottom: 15px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #5b21b6;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 9px;
            color: #666;
        }
        
        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
            color: #5b21b6;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .info-label {
            width: 140px;
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            flex: 1;
        }
        
        .purpose-box {
            background-color: #f3e8ff;
            border: 1px solid: #7c3aed;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        
        .purpose-title {
            font-weight: bold;
            color: #5b21b6;
            margin-bottom: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background-color: #7c3aed;
            color: white;
        }
        
        table th {
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #6d28d9;
        }
        
        table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        .status-purchased { background-color: #dbeafe; color: #1e40af; }
        
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            opacity: 0.08;
            color: #7c3aed;
            z-index: -1;
        }
    </style>
</head>
<body>
    @if($purchaseRequest->status === 'pending')
        <div class="watermark">DRAFT</div>
    @elseif($purchaseRequest->status === 'approved')
        <div class="watermark">APPROVED</div>
    @endif

    <!-- Header -->
    <div class="header">
        <div class="company-name">PT. NAMA PERUSAHAAN ANDA</div>
        <div class="company-address">Jl. Contoh Alamat No. 123, Jakarta 12345</div>
        <div class="company-address">Telp: (021) 1234-5678 | Email: purchasing@perusahaan.com</div>
        <div class="document-title">PURCHASE REQUEST / PENGAJUAN PEMBELIAN MATERIAL</div>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <table style="border: none; margin-bottom: 15px;">
            <tr>
                <td style="border: none; width: 50%; vertical-align: top;">
                    <div class="info-row">
                        <div class="info-label">No. Pengajuan</div>
                        <div class="info-value">: <strong>{{ $purchaseRequest->request_number }}</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Pengajuan</div>
                        <div class="info-value">: {{ $purchaseRequest->request_date->format('d F Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Diajukan Oleh</div>
                        <div class="info-value">: {{ $purchaseRequest->requester->full_name }}</div>
                    </div>
                </td>
                <td style="border: none; width: 50%; vertical-align: top;">
                    <div class="info-row">
                        <div class="info-label">No. Surat</div>
                        <div class="info-value">: {{ $purchaseRequest->letter_number }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Surat</div>
                        <div class="info-value">: {{ $purchaseRequest->letter_date->format('d F Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status</div>
                        <div class="info-value">: 
                            <span class="status-badge status-{{ $purchaseRequest->status }}">
                                {{ strtoupper($purchaseRequest->status) }}
                            </span>
                        </div>
                    </div>
                    @if($purchaseRequest->approved_by)
                        <div class="info-row">
                            <div class="info-label">Disetujui Oleh</div>
                            <div class="info-value">: {{ $purchaseRequest->approver->full_name }}</div>
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Purpose -->
    <div class="purpose-box">
        <div class="purpose-title">TUJUAN PEMBELIAN:</div>
        <div>{{ $purchaseRequest->purpose }}</div>
    </div>

    <!-- Materials Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 15%;">Kode</th>
                <th style="width: 40%;">Nama Material</th>
                <th style="width: 10%;" class="text-center">Satuan</th>
                <th style="width: 15%;" class="text-right">Qty Diajukan</th>
                <th style="width: 16%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseRequest->details as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $detail->material->material_code }}</td>
                    <td>{{ $detail->material->material_name }}</td>
                    <td class="text-center">{{ $detail->material->unit }}</td>
                    <td class="text-right font-bold">{{ number_format($detail->qty_requested, 2) }}</td>
                    <td style="font-size: 9px;">{{ $detail->notes ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot style="background-color: #f3e8ff;">
            <tr>
                <td colspan="4" class="text-right font-bold" style="color: #5b21b6;">TOTAL ITEM:</td>
                <td colspan="2" class="font-bold" style="color: #5b21b6;">{{ $purchaseRequest->details->count() }} Item</td>
            </tr>
        </tfoot>
    </table>

    <!-- Notes if any -->
    @if($purchaseRequest->notes)
        <div style="background-color: #fffbeb; border: 1px solid #fbbf24; border-radius: 5px; padding: 10px; margin-bottom: 20px;">
            <div style="font-weight: bold; color: #92400e; margin-bottom: 5px;">Catatan:</div>
            <div>{{ $purchaseRequest->notes }}</div>
        </div>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div>Diajukan Oleh,</div>
            <div class="signature-line">
                <strong>{{ $purchaseRequest->requester->full_name }}</strong><br>
                <span style="font-size: 9px;">{{ $purchaseRequest->requester->position ?? 'Staff' }}</span>
            </div>
        </div>
        
        <div class="signature-box">
            <div>Disetujui Oleh,</div>
            <div class="signature-line">
                @if($purchaseRequest->approver)
                    <strong>{{ $purchaseRequest->approver->full_name }}</strong><br>
                    <span style="font-size: 9px;">{{ $purchaseRequest->approver->position ?? 'Manager' }}</span>
                @else
                    <strong>(...........................)</strong><br>
                    <span style="font-size: 9px;">Manager</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>Dokumen ini dicetak secara otomatis pada {{ now()->format('d F Y H:i:s') }}</div>
        <div>{{ $purchaseRequest->request_number }} | Halaman 1 dari 1</div>
    </div>
</body>
</html>