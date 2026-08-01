<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $purchaseOrder->po_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #1a1a1a;
            background: #fff;
            padding: 0;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm 15mm 20mm 15mm;
            background: #fff;
        }

        /* ── HEADER ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 12px;
            border-bottom: 3px solid #1d4ed8;
            margin-bottom: 18px;
        }

        .company-block {}

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #1d4ed8;
            letter-spacing: 1px;
        }

        .company-name span {
            color: #ea580c;
        }

        .company-tagline {
            font-size: 10px;
            color: #6b7280;
            margin-top: 2px;
        }

        .company-address {
            font-size: 10px;
            color: #6b7280;
            margin-top: 4px;
            line-height: 1.5;
        }

        .doc-block {
            text-align: right;
        }

        .doc-title {
            font-size: 20px;
            font-weight: bold;
            color: #1d4ed8;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .doc-number {
            font-size: 13px;
            font-weight: bold;
            color: #1a1a1a;
            margin-top: 4px;
        }

        .doc-date {
            font-size: 10px;
            color: #6b7280;
            margin-top: 3px;
        }

        /* ── STATUS BADGE ── */
        .status-badge {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-pending   { background: #fef3c7; color: #92400e; border: 1px solid #fbbf24; }
        .status-completed { background: #dcfce7; color: #166534; border: 1px solid #4ade80; }
        .status-cancelled { background: #fee2e2; color: #991b1b; border: 1px solid #f87171; }

        /* ── INFO SECTION ── */
        .info-section {
            display: flex;
            gap: 16px;
            margin-bottom: 18px;
        }

        .info-box {
            flex: 1;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }

        .info-box-header {
            background: #1d4ed8;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 5px 10px;
        }

        .info-box-body {
            padding: 10px;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
            font-size: 11px;
            line-height: 1.5;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            color: #6b7280;
            width: 110px;
            flex-shrink: 0;
        }

        .info-value {
            color: #1a1a1a;
            font-weight: 500;
        }

        /* ── ITEMS TABLE ── */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1d4ed8;
            margin-bottom: 6px;
            border-left: 3px solid #1d4ed8;
            padding-left: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        thead tr {
            background: #1d4ed8;
            color: #fff;
        }

        thead th {
            padding: 8px 10px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }

        thead th.text-center { text-align: center; }
        thead th.text-right  { text-align: right; }

        tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:nth-child(even) {
            background: #f8faff;
        }

        tbody tr:last-child {
            border-bottom: 2px solid #1d4ed8;
        }

        tbody td {
            padding: 7px 10px;
            font-size: 11px;
            color: #1a1a1a;
            vertical-align: top;
        }

        .td-center { text-align: center; }
        .td-right  { text-align: right; }

        .product-code {
            font-size: 9px;
            color: #6b7280;
            margin-top: 1px;
        }

        /* ── TOTALS ── */
        .bottom-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .notes-box {
            flex: 1;
            margin-right: 20px;
        }

        .notes-content {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 8px 10px;
            font-size: 11px;
            color: #374151;
            line-height: 1.5;
            min-height: 60px;
        }

        .totals-box {
            width: 260px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 0;
        }

        .totals-table td {
            padding: 6px 10px;
            font-size: 11px;
            border-bottom: 1px solid #e5e7eb;
        }

        .totals-table tr:last-child td {
            border-bottom: none;
        }

        .totals-table .label-cell {
            color: #6b7280;
        }

        .totals-table .value-cell {
            text-align: right;
            color: #1a1a1a;
            font-weight: 500;
        }

        .grand-total-row td {
            background: #1d4ed8;
            color: #fff !important;
            font-weight: bold;
            font-size: 12px;
        }

        .grand-total-row .value-cell {
            color: #fff !important;
        }

        /* ── SIGNATURE ── */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .signature-box {
            text-align: center;
            width: 160px;
        }

        .signature-title {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 50px;
        }

        .signature-line {
            border-top: 1px solid #1a1a1a;
            padding-top: 5px;
            font-size: 11px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .signature-role {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ── FOOTER ── */
        .doc-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-left {
            font-size: 9px;
            color: #9ca3af;
        }

        .footer-right {
            font-size: 9px;
            color: #9ca3af;
            text-align: right;
        }

        /* ── PRINT BUTTON ── */
        .print-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #1d4ed8;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 9999;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .print-bar-title {
            font-size: 13px;
            font-weight: bold;
        }

        .print-bar-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 7px 18px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-print {
            background: #fff;
            color: #1d4ed8;
        }

        .btn-close {
            background: rgba(255,255,255,0.2);
            color: #fff;
        }

        .print-spacer {
            height: 50px;
        }

        @media print {
            .print-bar    { display: none; }
            .print-spacer { display: none; }
            body          { padding: 0; }
            .page         { padding: 10mm 12mm 15mm 12mm; margin: 0; }
        }
    </style>
</head>
<body>

    {{-- Print Bar --}}
    <div class="print-bar">
        <div class="print-bar-title">
            Preview: Purchase Order &nbsp;|&nbsp; {{ $purchaseOrder->po_number }}
        </div>
        <div class="print-bar-actions">
            <button class="btn btn-print" onclick="window.print()">🖨 Cetak / Simpan PDF</button>
            <button class="btn btn-close" onclick="window.close()">✕ Tutup</button>
        </div>
    </div>
    <div class="print-spacer"></div>

    <div class="page">

        {{-- ── HEADER ── --}}
        <div class="header">
            <div class="company-block">
                <div class="company-name">Heaven Spot <span>Indonesia</span></div>
                <div class="company-tagline">Sistem Inventori & Manajemen Penjualan</div>
                <div class="company-address">
                    Heaven Spot Indonesia &bull; Sistem dioperasikan secara digital
                </div>
            </div>
            <div class="doc-block">
                <div class="doc-title">Purchase Order</div>
                <div class="doc-number">{{ $purchaseOrder->po_number }}</div>
                <div class="doc-date">
                    Tanggal: {{ $purchaseOrder->purchase_date->format('d/m/Y') }}
                </div>
                <div class="doc-date">
                    Dibuat: {{ $purchaseOrder->created_at->format('d/m/Y H:i') }}
                </div>
                @php
                    $statusClass = match($purchaseOrder->status) {
                        'completed' => 'status-completed',
                        'cancelled' => 'status-cancelled',
                        default     => 'status-pending',
                    };
                    $statusLabel = match($purchaseOrder->status) {
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default     => 'Pending',
                    };
                @endphp
                <div>
                    <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>
            </div>
        </div>

        {{-- ── INFO SECTION ── --}}
        <div class="info-section">

            {{-- PO Info --}}
            <div class="info-box">
                <div class="info-box-header">Informasi Purchase Order</div>
                <div class="info-box-body">
                    <div class="info-row">
                        <span class="info-label">Nomor PO</span>
                        <span class="info-value">{{ $purchaseOrder->po_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tanggal Pembelian</span>
                        <span class="info-value">{{ $purchaseOrder->purchase_date->format('d F Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value">{{ $statusLabel }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Dibuat Oleh</span>
                        <span class="info-value">{{ auth()->user()->name }}</span>
                    </div>
                </div>
            </div>

            {{-- Supplier Info --}}
            <div class="info-box">
                <div class="info-box-header">Informasi Supplier</div>
                <div class="info-box-body">
                    <div class="info-row">
                        <span class="info-label">Nama Supplier</span>
                        <span class="info-value">{{ $purchaseOrder->supplier->name }}</span>
                    </div>
                    @if($purchaseOrder->supplier->contact_person)
                    <div class="info-row">
                        <span class="info-label">Contact Person</span>
                        <span class="info-value">{{ $purchaseOrder->supplier->contact_person }}</span>
                    </div>
                    @endif
                    @if($purchaseOrder->supplier->phone)
                    <div class="info-row">
                        <span class="info-label">Telepon</span>
                        <span class="info-value">{{ $purchaseOrder->supplier->phone }}</span>
                    </div>
                    @endif
                    @if($purchaseOrder->supplier->email)
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $purchaseOrder->supplier->email }}</span>
                    </div>
                    @endif
                    @if($purchaseOrder->supplier->address)
                    <div class="info-row">
                        <span class="info-label">Alamat</span>
                        <span class="info-value">{{ $purchaseOrder->supplier->address }}</span>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ── ITEMS TABLE ── --}}
        <div class="section-title">Daftar Item Pembelian</div>
        <table>
            <thead>
                <tr>
                    <th style="width:4%">No</th>
                    <th style="width:13%">Kode</th>
                    <th style="width:35%">Nama Produk</th>
                    <th style="width:8%" class="text-center">Qty</th>
                    <th style="width:8%" class="text-center">Satuan</th>
                    <th style="width:16%" class="text-right">Harga Satuan</th>
                    <th style="width:16%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td class="td-center">{{ $index + 1 }}</td>
                    <td>
                        {{ $item->product->code }}
                    </td>
                    <td>
                        {{ $item->product->name }}
                        @if($item->product->brand)
                        <div class="product-code">{{ $item->product->brand }}</div>
                        @endif
                    </td>
                    <td class="td-center">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                    <td class="td-center">{{ $item->product->unit }}</td>
                    <td class="td-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="td-right">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ── BOTTOM: NOTES + TOTALS ── --}}
        <div class="bottom-section">

            {{-- Notes --}}
            <div class="notes-box">
                <div class="section-title">Catatan</div>
                <div class="notes-content">
                    {{ $purchaseOrder->notes ?: 'Tidak ada catatan.' }}
                </div>
            </div>

            {{-- Totals --}}
            <div class="totals-box">
                <div class="section-title">Rincian Pembayaran</div>
                <table class="totals-table">
                    <tr>
                        <td class="label-cell">Subtotal</td>
                        <td class="value-cell">Rp {{ number_format($purchaseOrder->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($purchaseOrder->discount_percentage > 0)
                    <tr>
                        <td class="label-cell">Diskon ({{ number_format($purchaseOrder->discount_percentage, 0) }}%)</td>
                        <td class="value-cell">- Rp {{ number_format($purchaseOrder->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($purchaseOrder->tax_percentage > 0)
                    <tr>
                        <td class="label-cell">PPN ({{ number_format($purchaseOrder->tax_percentage, 0) }}%)</td>
                        <td class="value-cell">Rp {{ number_format($purchaseOrder->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="grand-total-row">
                        <td class="label-cell">Grand Total</td>
                        <td class="value-cell">Rp {{ number_format($purchaseOrder->grand_total, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

        </div>

        {{-- ── SIGNATURE ── --}}
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Dibuat Oleh</div>
                <div class="signature-line">{{ auth()->user()->name }}</div>
                <div class="signature-role">{{ auth()->user()->getRoleNames()->first() ?? 'Staff' }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Disetujui Oleh</div>
                <div class="signature-line">&nbsp;</div>
                <div class="signature-role">Manager / Owner</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Pihak Supplier</div>
                <div class="signature-line">{{ $purchaseOrder->supplier->name }}</div>
                <div class="signature-role">Supplier</div>
            </div>
        </div>

        {{-- ── FOOTER ── --}}
        <div class="doc-footer">
            <div class="footer-left">
                Dokumen ini digenerate secara otomatis oleh sistem Heaven Spot Indonesia.<br>
                Dicetak pada: {{ now()->format('d/m/Y H:i') }} &bull; Oleh: {{ auth()->user()->name }}
            </div>
            <div class="footer-right">
                <strong>Heaven Spot Indonesia</strong><br>
                {{ $purchaseOrder->po_number }}
            </div>
        </div>

    </div>{{-- end .page --}}

</body>
</html>
