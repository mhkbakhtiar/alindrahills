<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $detail->worker->full_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 30px;
        }
        
        .slip-container {
            border: 2px solid #2563eb;
            border-radius: 10px;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .slip-title {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            margin-top: 10px;
        }
        
        .worker-info {
            background-color: #dbeafe;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .worker-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            width: 150px;
            color: #1e40af;
            font-weight: bold;
        }
        
        .info-value {
            flex: 1;
        }
        
        .calculation-section {
            margin: 20px 0;
        }
        
        .calc-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #ddd;
        }
        
        .calc-label {
            font-weight: bold;
        }
        
        .calc-value {
            text-align: right;
            min-width: 150px;
        }
        
        .total-row {
            background-color: #dbeafe;
            padding: 15px;
            margin-top: 10px;
            border-radius: 5px;
        }
        
        .total-label {
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
        }
        
        .total-value {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
        }
        
        .notes-section {
            background-color: #fffbeb;
            border: 1px solid #fbbf24;
            border-radius: 5px;
            padding: 10px;
            margin: 20px 0;
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
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            opacity: 0.05;
            color: #2563eb;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">SLIP GAJI</div>
    
    <div class="slip-container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">PT. NAMA PERUSAHAAN ANDA</div>
            <div style="font-size: 10px; color: #666;">Jl. Contoh Alamat No. 123, Jakarta 12345</div>
            <div class="slip-title">SLIP PEMBAYARAN GAJI</div>
            <div style="font-size: 10px; margin-top: 5px;">{{ $payrollRequest->request_number }}</div>
        </div>

        <!-- Worker Info -->
        <div class="worker-info">
            <div class="worker-name">{{ $detail->worker->full_name }}</div>
            <div class="info-row">
                <div class="info-label">Periode</div>
                <div class="info-value">: {{ $payrollRequest->period_start->format('d M Y') }} - {{ $payrollRequest->period_end->format('d M Y') }}</div>
            </div>
            @if($payrollRequest->activity)
                <div class="info-row">
                    <div class="info-label">Kegiatan</div>
                    <div class="info-value">: {{ $payrollRequest->activity->activity_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Lokasi</div>
                    <div class="info-value">: {{ $payrollRequest->activity->location->location_name ?? '-' }}</div>
                </div>
            @endif
            <div class="info-row">
                <div class="info-label">Tanggal Cetak</div>
                <div class="info-value">: {{ now()->format('d F Y') }}</div>
            </div>
        </div>

        <!-- Calculation -->
        <div class="calculation-section">
            <div class="calc-row">
                <div class="calc-label">Hari Kerja</div>
                <div class="calc-value">{{ number_format($detail->days_worked, 1) }} hari</div>
            </div>
            
            <div class="calc-row">
                <div class="calc-label">Upah per Hari</div>
                <div class="calc-value">Rp {{ number_format($detail->daily_rate, 0, ',', '.') }}</div>
            </div>
            
            <div class="calc-row" style="background-color: #f9fafb; padding: 10px; margin: 10px -10px;">
                <div class="calc-label" style="color: #2563eb;">Total Upah</div>
                <div class="calc-value" style="font-weight: bold; color: #2563eb;">Rp {{ number_format($detail->total_wage, 0, ',', '.') }}</div>
            </div>
            
            @if($detail->bonus > 0)
                <div class="calc-row">
                    <div class="calc-label" style="color: #059669;">Bonus</div>
                    <div class="calc-value" style="color: #059669;">+ Rp {{ number_format($detail->bonus, 0, ',', '.') }}</div>
                </div>
            @endif
            
            @if($detail->deduction > 0)
                <div class="calc-row">
                    <div class="calc-label" style="color: #dc2626;">Potongan</div>
                    <div class="calc-value" style="color: #dc2626;">- Rp {{ number_format($detail->deduction, 0, ',', '.') }}</div>
                </div>
            @endif
            
            <div class="total-row">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="total-label">TOTAL BERSIH DITERIMA</div>
                    <div class="total-value">Rp {{ number_format($detail->net_payment, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($detail->notes || $payrollRequest->notes)
            <div class="notes-section">
                <div style="font-weight: bold; margin-bottom: 5px;">Catatan:</div>
                @if($detail->notes)
                    <div>{{ $detail->notes }}</div>
                @endif
                @if($payrollRequest->notes)
                    <div style="margin-top: 5px; font-size: 10px; color: #666;">{{ $payrollRequest->notes }}</div>
                @endif
            </div>
        @endif

        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-box">
                <div style="font-size: 11px;">Penerima,</div>
                <div class="signature-line">
                    <strong>{{ $detail->worker->full_name }}</strong>
                </div>
            </div>
            
            <div class="signature-box">
                <div style="font-size: 11px;">Hormat Kami,</div>
                <div class="signature-line">
                    <strong>{{ $payrollRequest->requester->full_name }}</strong>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>Slip ini dicetak otomatis dan sah tanpa tanda tangan basah</div>
            <div style="margin-top: 3px;">Dicetak pada: {{ now()->format('d F Y H:i:s') }}</div>
        </div>
    </div>
</body>
</html>