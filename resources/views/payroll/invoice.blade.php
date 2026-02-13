<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Pengajuan Penggajian - {{ $payrollRequest->request_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 9px;
            color: #666;
            margin-bottom: 3px;
        }
        
        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
            color: #1e40af;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .info-label {
            width: 150px;
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            flex: 1;
        }
        
        .activity-box {
            background-color: #dbeafe;
            border: 1px solid #3b82f6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        
        .activity-title {
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background-color: #2563eb;
            color: white;
        }
        
        table th {
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #1e40af;
        }
        
        table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        table tbody tr:hover {
            background-color: #f3f4f6;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        .total-row {
            background-color: #f3f4f6 !important;
            font-weight: bold;
        }
        
        .grand-total {
            background-color: #dbeafe !important;
            font-weight: bold;
            font-size: 11px;
        }
        
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
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .status-paid {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        
        .notes-box {
            background-color: #fffbeb;
            border: 1px solid #fbbf24;
            border-radius: 5px;
            padding: 10px;
            margin-top: 20px;
        }
        
        .notes-title {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            opacity: 0.1;
            color: #2563eb;
            z-index: -1;
        }
    </style>
</head>
<body>
    @if($payrollRequest->status === 'pending')
        <div class="watermark">DRAFT</div>
    @elseif($payrollRequest->status === 'approved')
        <div class="watermark">APPROVED</div>
    @elseif($payrollRequest->status === 'paid')
        <div class="watermark">PAID</div>
    @endif

    <!-- Header -->
    <div class="header">
        <div class="company-name">PT. NAMA PERUSAHAAN ANDA</div>
        <div class="company-address">Jl. Contoh Alamat No. 123, Jakarta 12345</div>
        <div class="company-address">Telp: (021) 1234-5678 | Email: info@perusahaan.com</div>
        <div class="document-title">INVOICE PENGAJUAN PENGGAJIAN TUKANG</div>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <table style="border: none; margin-bottom: 10px;">
            <tr>
                <td style="border: none; width: 50%; vertical-align: top;">
                    <div class="info-row">
                        <div class="info-label">No. Pengajuan</div>
                        <div class="info-value">: <strong>{{ $payrollRequest->request_number }}</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Pengajuan</div>
                        <div class="info-value">: {{ $payrollRequest->request_date->format('d F Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Periode Penggajian</div>
                        <div class="info-value">: {{ $payrollRequest->period_start->format('d M Y') }} s/d {{ $payrollRequest->period_end->format('d M Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Diajukan Oleh</div>
                        <div class="info-value">: {{ $payrollRequest->requester->full_name }}</div>
                    </div>
                </td>
                <td style="border: none; width: 50%; vertical-align: top;">
                    <div class="info-row">
                        <div class="info-label">No. Surat</div>
                        <div class="info-value">: {{ $payrollRequest->letter_number }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Surat</div>
                        <div class="info-value">: {{ \Carbon\Carbon::parse($payrollRequest->letter_date)->format('d F Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status</div>
                        <div class="info-value">: 
                            <span class="status-badge status-{{ $payrollRequest->status }}">
                                {{ strtoupper($payrollRequest->status) }}
                            </span>
                        </div>
                    </div>
                    @if($payrollRequest->approved_by)
                        <div class="info-row">
                            <div class="info-label">Disetujui Oleh</div>
                            <div class="info-value">: {{ $payrollRequest->approver->full_name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tanggal Persetujuan</div>
                            <div class="info-value">: {{ $payrollRequest->approved_date->format('d F Y H:i') }}</div>
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Activity Info (if exists) -->
    @if($payrollRequest->activity)
        <div class="activity-box">
            <div class="activity-title">Kegiatan Terkait:</div>
            <div><strong>{{ $payrollRequest->activity->activity_code }}</strong> - {{ $payrollRequest->activity->activity_name }}</div>
            <div style="font-size: 9px; color: #1e40af;">
                {{ $payrollRequest->activity->location->location_name ?? '-' }}
            </div>
        </div>
    @endif

    <!-- Workers Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 20%;">Nama Tukang</th>
                <th style="width: 10%;" class="text-center">Hari Kerja</th>
                <th style="width: 14%;" class="text-right">Upah/Hari</th>
                <th style="width: 14%;" class="text-right">Total Upah</th>
                <th style="width: 12%;" class="text-right">Bonus</th>
                <th style="width: 12%;" class="text-right">Potongan</th>
                <th style="width: 14%;" class="text-right">Total Bersih</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrollRequest->details as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $detail->worker->full_name }}</strong>
                        @if($detail->notes)
                            <br><span style="font-size: 9px; color: #666;">{{ $detail->notes }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($detail->days_worked, 1) }}</td>
                    <td class="text-right">Rp {{ number_format($detail->daily_rate, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($detail->total_wage, 0, ',', '.') }}</td>
                    <td class="text-right">
                        @if($detail->bonus > 0)
                            <span style="color: #059669;">+Rp {{ number_format($detail->bonus, 0, ',', '.') }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($detail->deduction > 0)
                            <span style="color: #dc2626;">-Rp {{ number_format($detail->deduction, 0, ',', '.') }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right font-bold">Rp {{ number_format($detail->net_payment, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="grand-total">
                <td colspan="7" class="text-right">GRAND TOTAL:</td>
                <td class="text-right">Rp {{ number_format($payrollRequest->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Summary Info -->
    <div style="background-color: #f9fafb; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between;">
            <div style="width: 32%; text-align: center;">
                <div style="font-size: 9px; color: #666;">Total Tukang</div>
                <div style="font-size: 16px; font-weight: bold; color: #2563eb;">{{ $payrollRequest->details->count() }}</div>
            </div>
            <div style="width: 32%; text-align: center;">
                <div style="font-size: 9px; color: #666;">Total Hari Kerja</div>
                <div style="font-size: 16px; font-weight: bold; color: #059669;">{{ number_format($payrollRequest->details->sum('days_worked'), 1) }}</div>
            </div>
            <div style="width: 32%; text-align: center;">
                <div style="font-size: 9px; color: #666;">Total Pembayaran</div>
                <div style="font-size: 14px; font-weight: bold; color: #dc2626;">Rp {{ number_format($payrollRequest->total_amount, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Notes (if any) -->
    @if($payrollRequest->notes)
        <div class="notes-box">
            <div class="notes-title">Catatan:</div>
            <div>{{ $payrollRequest->notes }}</div>
        </div>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div>Diajukan Oleh,</div>
            <div class="signature-line">
                <strong>{{ $payrollRequest->requester->full_name }}</strong><br>
                <span style="font-size: 9px;">{{ $payrollRequest->requester->position ?? 'Staff Teknik' }}</span>
            </div>
        </div>
        
        <div class="signature-box">
            <div>
                @if($payrollRequest->status === 'approved' || $payrollRequest->status === 'paid')
                    Disetujui Oleh,
                @else
                    Mengetahui,
                @endif
            </div>
            <div class="signature-line">
                @if($payrollRequest->approver)
                    <strong>{{ $payrollRequest->approver->full_name }}</strong><br>
                    <span style="font-size: 9px;">{{ $payrollRequest->approver->position ?? 'Manager' }}</span>
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
        <div>{{ $payrollRequest->request_number }} | Halaman 1 dari 1</div>
    </div>
</body>
</html>