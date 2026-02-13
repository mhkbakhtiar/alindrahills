<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Goods Receipt - {{ $receipt->receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333;
        }
        
        .container {
            padding: 15px;
        }
        
        /* Header Section */
        .header {
            border-bottom: 3px solid #10b981;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .company-info {
            display: table;
            width: 100%;
        }
        
        .company-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .company-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }
        
        .company-name {
            font-size: 16pt;
            font-weight: bold;
            color: #10b981;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 8pt;
            color: #666;
            line-height: 1.5;
        }
        
        .document-title {
            font-size: 14pt;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }
        
        .document-number {
            font-size: 10pt;
            color: #10b981;
            font-weight: bold;
        }
        
        /* Status Badges */
        .status-badges {
            margin: 10px 0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .status-received {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }
        
        .status-corrected {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
        }
        
        .status-partial {
            background-color: #fed7aa;
            color: #9a3412;
            border: 1px solid #f97316;
        }
        
        .status-complete {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }
        
        .status-pending {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #3b82f6;
        }
        
        .status-correction-note {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
        }
        
        /* Purchase Order Box */
        .po-box {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 2px solid #3b82f6;
            border-radius: 6px;
            padding: 12px;
            margin: 15px 0;
        }
        
        .po-title {
            font-size: 9pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .po-number {
            font-size: 11pt;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 4px;
        }
        
        .po-supplier {
            font-size: 8pt;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .po-note {
            background-color: rgba(59, 130, 246, 0.1);
            padding: 6px;
            border-radius: 4px;
            font-size: 7pt;
            color: #1e40af;
            margin-top: 5px;
        }
        
        /* Info Grid */
        .info-section {
            margin: 15px 0;
        }
        
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #333;
            margin: 15px 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 2px solid #10b981;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 30%;
            padding: 6px 10px;
            background-color: #f9fafb;
            font-weight: 600;
            font-size: 8pt;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
        }
        
        .info-value {
            display: table-cell;
            width: 70%;
            padding: 6px 10px;
            font-size: 8pt;
            border-bottom: 1px solid #e5e7eb;
        }
        
        /* Material Table */
        table.material-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 7pt;
        }
        
        thead {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        th {
            padding: 6px 4px;
            text-align: left;
            font-weight: 600;
            border-right: 1px solid rgba(255,255,255,0.3);
        }
        
        th:last-child {
            border-right: none;
        }
        
        th.text-center {
            text-align: center;
        }
        
        th.text-right {
            text-align: right;
        }
        
        tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        tbody tr.correction-row {
            background-color: #fef3c7;
        }
        
        tbody tr.history-row {
            background-color: #dbeafe;
        }
        
        td {
            padding: 5px 4px;
            vertical-align: top;
        }
        
        td.text-center {
            text-align: center;
        }
        
        td.text-right {
            text-align: right;
        }
        
        .material-code {
            font-weight: bold;
            color: #1f2937;
        }
        
        .qty-highlight {
            font-weight: bold;
            color: #10b981;
        }
        
        .qty-diff-positive {
            color: #059669;
            font-weight: bold;
        }
        
        .qty-diff-negative {
            color: #dc2626;
            font-weight: bold;
        }
        
        /* Condition Badges */
        .condition-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: 600;
        }
        
        .condition-good {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .condition-damaged {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .condition-incomplete {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        /* History Section */
        .history-section {
            background-color: #eff6ff;
            border-left: 3px solid #3b82f6;
            padding: 8px;
            margin: 5px 0;
            font-size: 7pt;
        }
        
        .history-title {
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .history-item {
            padding: 3px 0;
            border-bottom: 1px solid #bfdbfe;
        }
        
        .history-item:last-child {
            border-bottom: none;
        }
        
        .history-current {
            font-weight: bold;
            color: #1e40af;
        }
        
        .history-total {
            margin-top: 5px;
            padding-top: 5px;
            border-top: 2px solid #3b82f6;
            font-weight: bold;
            color: #1e40af;
        }
        
        /* Summary Cards */
        .summary-section {
            margin: 15px 0;
            display: table;
            width: 100%;
        }
        
        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }
        
        .summary-label {
            font-size: 7pt;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .summary-value {
            font-size: 14pt;
            font-weight: bold;
        }
        
        .summary-blue { color: #3b82f6; }
        .summary-green { color: #10b981; }
        .summary-yellow { color: #f59e0b; }
        .summary-purple { color: #8b5cf6; }
        
        /* PO Progress */
        .progress-section {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 6px;
            padding: 10px;
            margin: 15px 0;
        }
        
        .progress-title {
            font-size: 9pt;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 8px;
        }
        
        .progress-item {
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #fde68a;
        }
        
        .progress-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .progress-material {
            font-size: 8pt;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 3px;
        }
        
        .progress-stats {
            font-size: 7pt;
            color: #6b7280;
            margin-bottom: 3px;
        }
        
        .progress-bar-container {
            width: 100%;
            height: 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 3px;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6 0%, #10b981 100%);
        }
        
        .progress-note {
            font-size: 7pt;
            margin-top: 3px;
        }
        
        .progress-note.complete {
            color: #065f46;
        }
        
        .progress-note.remaining {
            color: #92400e;
        }
        
        .progress-info {
            background-color: white;
            padding: 6px;
            border-radius: 4px;
            font-size: 7pt;
            color: #92400e;
            margin-top: 8px;
        }
        
        /* Complete Box */
        .complete-box {
            background-color: #d1fae5;
            border: 2px solid #10b981;
            border-radius: 6px;
            padding: 10px;
            margin: 15px 0;
        }
        
        .complete-title {
            font-size: 9pt;
            font-weight: bold;
            color: #065f46;
            margin-bottom: 3px;
        }
        
        .complete-text {
            font-size: 7pt;
            color: #065f46;
        }
        
        /* Correction Box */
        .correction-box {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 6px;
            padding: 10px;
            margin: 15px 0;
        }
        
        .correction-title {
            font-size: 9pt;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 8px;
        }
        
        .correction-list {
            margin-left: 15px;
            color: #78350f;
            font-size: 7pt;
        }
        
        .correction-list li {
            margin-bottom: 4px;
        }
        
        /* Signatures */
        .signature-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 8px;
        }
        
        .signature-title {
            font-size: 8pt;
            font-weight: 600;
            margin-bottom: 40px;
            color: #6b7280;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            padding-top: 4px;
            font-size: 8pt;
            font-weight: bold;
        }
        
        .signature-date {
            font-size: 7pt;
            color: #6b7280;
            margin-top: 2px;
        }
        
        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 7pt;
            color: #6b7280;
        }
        
        /* Page Break */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-left">
                    <div class="company-name">PT NAMA PERUSAHAAN ANDA</div>
                    <div class="company-details">
                        Jl. Alamat Perusahaan No. 123, Jakarta<br>
                        Telp: (021) 1234-5678 | Email: info@perusahaan.com<br>
                        NPWP: 01.234.567.8-901.000
                    </div>
                </div>
                <div class="company-right">
                    <div class="document-title">GOODS RECEIPT</div>
                    <div class="document-number">{{ $receipt->receipt_number }}</div>
                </div>
            </div>
        </div>
        
        <!-- Status Badges -->
        <div class="status-badges">
            @if($receipt->status === 'received')
                <span class="status-badge status-received">RECEIVED</span>
            @elseif($receipt->status === 'corrected')
                <span class="status-badge status-corrected">CORRECTED</span>
            @else
                <span class="status-badge status-received">{{ strtoupper($receipt->status) }}</span>
            @endif

            @if($receipt->is_corrected)
                <span class="status-badge status-correction-note">Ada Koreksi Qty/Kondisi</span>
            @endif

            @php
                $purchase = $receipt->purchase;
                $isPartialReceipt = false;
                $isFullyReceived = false;
                
                if ($purchase) {
                    $isFullyReceived = $purchase->isFullyReceived();
                    foreach ($receipt->details as $detail) {
                        if ($detail->qty_received < $detail->qty_ordered) {
                            $isPartialReceipt = true;
                            break;
                        }
                    }
                }
            @endphp

            @if($isPartialReceipt)
                <span class="status-badge status-partial">Partial Receipt</span>
            @endif

            @if($isFullyReceived)
                <span class="status-badge status-complete">PO Fully Received</span>
            @elseif($purchase)
                <span class="status-badge status-pending">Masih Ada Sisa</span>
            @endif
        </div>
        
        <!-- Purchase Order Info -->
        @if($receipt->purchase)
        <div class="po-box">
            <div class="po-title">PURCHASE ORDER TERKAIT</div>
            <div class="po-number">{{ $receipt->purchase->purchase_number }}</div>
            <div class="po-supplier">
                <strong>Supplier:</strong> {{ $receipt->purchase->supplier_name }}
                @if($receipt->purchase->supplier_contact)
                    | {{ $receipt->purchase->supplier_contact }}
                @endif
            </div>
            
            @php
                $totalReceipts = $receipt->purchase->goodsReceipts->count();
            @endphp
            
            @if($totalReceipts > 1)
                <div class="po-note">
                    <strong>Multiple Receipts:</strong> PO ini memiliki {{ $totalReceipts }} penerimaan barang
                    @if(!$isFullyReceived)
                        <span style="color: #9a3412;">(Belum lengkap semua)</span>
                    @endif
                </div>
            @endif
        </div>
        @endif
        
        <!-- Receipt Information -->
        <div class="info-section">
            <div class="section-title">INFORMASI PENERIMAAN</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Nomor Receipt</div>
                    <div class="info-value">{{ $receipt->receipt_number }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Penerimaan</div>
                    <div class="info-value">{{ $receipt->receipt_date->format('d F Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Diterima Oleh</div>
                    <div class="info-value">{{ $receipt->receiver->full_name ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Warehouse Tujuan</div>
                    <div class="info-value">{{ $receipt->warehouse->warehouse_name ?? '-' }}</div>
                </div>
                @if($receipt->notes)
                <div class="info-row">
                    <div class="info-label">Catatan</div>
                    <div class="info-value">{{ $receipt->notes }}</div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Material Details -->
        <div class="section-title">DAFTAR MATERIAL YANG DITERIMA</div>
        <table class="material-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 10%;">Kode</th>
                    <th style="width: 20%;">Nama Material</th>
                    <th class="text-center" style="width: 6%;">Satuan</th>
                    <th class="text-right" style="width: 8%;">Total PO</th>
                    <th class="text-right" style="width: 8%;">Terima Ini</th>
                    <th class="text-right" style="width: 7%;">Selisih</th>
                    <th class="text-center" style="width: 9%;">Kondisi</th>
                    <th class="text-center" style="width: 9%;">Status</th>
                    <th style="width: 19%;">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipt->details as $index => $detail)
                    @php
                        $poDetail = $receipt->purchase->details->where('material_id', $detail->material_id)->first();
                        $totalPO = $poDetail ? $poDetail->qty_ordered : $detail->qty_ordered;
                        $diff = $detail->qty_received - $totalPO;
                        $hasCorrection = $diff != 0 || $detail->condition_status !== 'good';
                        
                        $materialReceipts = $receipt->purchase->goodsReceipts()
                            ->with('details')
                            ->orderBy('receipt_date', 'asc')
                            ->get()
                            ->map(function($gr) use ($detail, $receipt) {
                                $grDetail = $gr->details->where('material_id', $detail->material_id)->first();
                                if ($grDetail) {
                                    return [
                                        'receipt_number' => $gr->receipt_number,
                                        'receipt_date' => $gr->receipt_date,
                                        'qty_received' => $grDetail->qty_received,
                                        'is_current' => $gr->receipt_id === $receipt->receipt_id
                                    ];
                                }
                                return null;
                            })
                            ->filter();
                            
                        $totalReceived = $materialReceipts->sum('qty_received');
                        $remaining = $totalPO - $totalReceived;
                    @endphp
                    <tr class="{{ $hasCorrection ? 'correction-row' : '' }}">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="material-code">{{ $detail->material->material_code }}</td>
                        <td>{{ $detail->material->material_name }}</td>
                        <td class="text-center">{{ $detail->material->unit }}</td>
                        <td class="text-right">{{ number_format($totalPO, 2) }}</td>
                        <td class="text-right qty-highlight">{{ number_format($detail->qty_received, 2) }}</td>
                        <td class="text-right">
                            @if($materialReceipts->count() > 1)
                                <span style="font-size: 6pt; color: #3b82f6;">Lihat History</span>
                            @else
                                @if($diff != 0)
                                    <span class="{{ $diff > 0 ? 'qty-diff-positive' : 'qty-diff-negative' }}">
                                        {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 2) }}
                                    </span>
                                @else
                                    -
                                @endif
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="condition-badge condition-{{ $detail->condition_status }}">
                                @if($detail->condition_status === 'good')
                                    Baik
                                @elseif($detail->condition_status === 'damaged')
                                    Rusak
                                @else
                                    Tidak Lengkap
                                @endif
                            </span>
                        </td>
                        <td class="text-center" style="font-size: 7pt;">
                            @if($detail->condition_status === 'good')
                                <span style="color: #065f46;">Masuk Stock</span>
                            @else
                                <span style="color: #991b1b;">Tidak Masuk Stock</span>
                            @endif
                        </td>
                        <td style="font-size: 7pt;">{{ $detail->notes ?? '-' }}</td>
                    </tr>
                    
                    <!-- Receipt History -->
                    @if($materialReceipts->count() > 1)
                        <tr class="history-row">
                            <td colspan="10">
                                <div class="history-section">
                                    <div class="history-title">History Penerimaan Material Ini:</div>
                                    @foreach($materialReceipts as $idx => $hist)
                                        <div class="history-item {{ $hist['is_current'] ? 'history-current' : '' }}">
                                            {{ $idx + 1 }}. {{ $hist['receipt_number'] }} | 
                                            {{ $hist['receipt_date']->format('d M Y') }} | 
                                            <strong>{{ number_format($hist['qty_received'], 2) }} {{ $detail->material->unit }}</strong>
                                            @if($hist['is_current'])
                                                <span style="background-color: #d1fae5; padding: 1px 4px; border-radius: 2px; margin-left: 5px;">← Receipt Ini</span>
                                            @endif
                                        </div>
                                    @endforeach
                                    <div class="history-total">
                                        TOTAL DITERIMA: {{ number_format($totalReceived, 2) }} / {{ number_format($totalPO, 2) }} {{ $detail->material->unit }}
                                    </div>
                                    @if($remaining > 0)
                                        <div class="progress-note remaining">
                                            Sisa yang belum diterima: <strong>{{ number_format($remaining, 2) }} {{ $detail->material->unit }}</strong>
                                        </div>
                                    @elseif($remaining == 0)
                                        <div class="progress-note complete">
                                            Material ini sudah lengkap diterima
                                        </div>
                                    @else
                                        <div class="progress-note" style="color: #991b1b;">
                                            Over receipt: <strong>{{ number_format(abs($remaining), 2) }} {{ $detail->material->unit }}</strong> lebih dari PO
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="10" class="text-center" style="padding: 15px;">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Correction Box -->
        @if($receipt->is_corrected)
        <div class="correction-box">
            <div class="correction-title">KETERANGAN KOREKSI</div>
            <ul class="correction-list">
                @foreach($receipt->details as $detail)
                    @php
                        $diff = $detail->qty_received - $detail->qty_ordered;
                    @endphp
                    @if($diff != 0)
                        <li>
                            <strong>{{ $detail->material->material_name }}:</strong> 
                            Qty berbeda 
                            <span style="{{ $diff > 0 ? 'color: #065f46;' : 'color: #991b1b;' }}">
                                ({{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 2) }} {{ $detail->material->unit }})
                            </span>
                            - {{ $diff < 0 ? 'Kurang dari order' : 'Lebih dari order' }}
                        </li>
                    @endif
                    @if($detail->condition_status !== 'good')
                        <li>
                            <strong>{{ $detail->material->material_name }}:</strong> 
                            Kondisi <span style="color: #991b1b;">{{ ucfirst($detail->condition_status) }}</span> - Tidak masuk stock
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endif
        
        <!-- Summary Cards -->
        <div class="summary-section">
            <div class="summary-card">
                <div class="summary-label">Total Item</div>
                <div class="summary-value summary-blue">{{ $receipt->details->count() }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Qty Terima</div>
                <div class="summary-value summary-green">{{ number_format($receipt->details->sum('qty_received'), 0) }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Item Koreksi</div>
                <div class="summary-value summary-yellow">
                    {{ $receipt->details->filter(fn($d) => $d->qty_received != $d->qty_ordered || $d->condition_status !== 'good')->count() }}
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Masuk Stock</div>
                <div class="summary-value summary-purple">
                    {{ $receipt->details->filter(fn($d) => $d->condition_status === 'good')->count() }}
                </div>
            </div>
        </div>
        
        <!-- PO Progress or Complete Status -->
        @if($purchase && !$isFullyReceived)
            <div class="progress-section">
                <div class="progress-title">STATUS PENERIMAAN PO</div>
                
                @foreach($purchase->details as $poDetail)
                    @php
                        $totalReceived = $purchase->goodsReceipts()
                            ->whereHas('details', function($q) use ($poDetail) {
                                $q->where('material_id', $poDetail->material_id);
                            })
                            ->get()
                            ->flatMap->details
                            ->where('material_id', $poDetail->material_id)
                            ->sum('qty_received');
                        
                        $remaining = $poDetail->qty_ordered - $totalReceived;
                        $percentage = ($totalReceived / $poDetail->qty_ordered) * 100;
                    @endphp
                    
                    <div class="progress-item">
                        <div class="progress-material">
                            {{ $poDetail->material->material_code }} - {{ $poDetail->material->material_name }}
                        </div>
                        <div class="progress-stats">
                            {{ number_format($totalReceived, 2) }} / {{ number_format($poDetail->qty_ordered, 2) }} {{ $poDetail->material->unit }}
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                        </div>
                        @if($remaining > 0)
                            <div class="progress-note remaining">
                                Sisa: <strong>{{ number_format($remaining, 2) }} {{ $poDetail->material->unit }}</strong> belum diterima
                            </div>
                        @else
                            <div class="progress-note complete">
                                Lengkap
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="progress-info">
                    💡 <strong>Info:</strong> Masih ada material yang belum lengkap diterima. 
                    Buat Goods Receipt baru untuk mencatat penerimaan sisanya.
                </div>
            </div>
        @elseif($purchase && $isFullyReceived)
            <div class="complete-box">
                <div class="complete-title">Purchase Order Lengkap</div>
                <div class="complete-text">
                    Semua material dari PO {{ $purchase->purchase_number }} sudah diterima lengkap
                    @if($purchase->goodsReceipts->count() > 1)
                        melalui {{ $purchase->goodsReceipts->count() }} penerimaan bertahap
                    @endif
                </div>
            </div>
        @endif
        
        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Yang Menyerahkan</div>
                <div class="signature-line">
                    ( _________________ )
                </div>
                <div class="signature-date">Supplier</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Yang Menerima</div>
                <div class="signature-line">
                    <strong>{{ $receipt->receiver->full_name ?? '________________' }}</strong>
                </div>
                <div class="signature-date">{{ $receipt->receipt_date->format('d M Y') }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Mengetahui</div>
                <div class="signature-line">
                    ( _________________ )
                </div>
                <div class="signature-date">Supervisor/Manager</div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            Dokumen ini dicetak secara otomatis pada {{ now()->format('d F Y H:i') }}<br>
            {{ $receipt->receipt_number }} | {{ $receipt->purchase->purchase_number ?? '-' }}
        </div>
    </div>
</body>
</html>