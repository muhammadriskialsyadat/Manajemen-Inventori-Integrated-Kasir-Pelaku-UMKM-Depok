<!-- resources/views/reports/pdf-template.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ ucfirst(str_replace('_', ' ', $reportType)) }} - UMKM Kota Depok</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .brand-blue   { color: #60a5fa; }
        .brand-orange { color: #fb923c; }


        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-period {
            font-size: 12px;
            color: #666;
        }

        .stats-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
        }

        .stat-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #f1f5f9;
            border: 1px solid #ddd;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }

        td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 10px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }

        .footer-left {
            float: left;
        }

        .footer-right {
            float: right;
        }

        .status-normal {
            color: #16a34a;
        }

        .status-low {
            color: #eab308;
        }

        .status-out {
            color: #dc2626;
        }

        .type-in {
            color: #16a34a;
        }

        .type-out {
            color: #dc2626;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">
            <span class="brand-blue">UMKM</span>
            <span class="brand-orange"> &bull; Kota Depok</span>
        </div>
        <div>Sistem Inventori UMKM Kota Depok</div>
        <div class="report-title">
            @if($reportType === 'stock')
            LAPORAN STOK PRODUK
            @elseif($reportType === 'sales')
            LAPORAN PENJUALAN
            @elseif($reportType === 'purchase')
            LAPORAN PEMBELIAN
            @elseif($reportType === 'tax')
            LAPORAN PAJAK PERTAMBAHAN NILAI (PPN)
            @else
            LAPORAN PERGERAKAN STOK
            @endif
        </div>
        @if(isset($reportData['period']))
        <div class="report-period">
            Periode: {{ \Carbon\Carbon::parse($reportData['period']['start'])->format('d/m/Y') }} -
            {{ \Carbon\Carbon::parse($reportData['period']['end'])->format('d/m/Y') }}
        </div>
        @endif
        <div class="report-period">
            Dicetak pada: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    @if($reportType === 'stock')
    <div class="stats-section">
        <div class="stat-item">
            <div class="stat-value">{{ $reportData['total_products'] }}</div>
            <div class="stat-label">Total Produk</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $reportData['low_stock_products']->count() }}</div>
            <div class="stat-label">Stok Menipis</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $reportData['out_of_stock_products']->count() }}</div>
            <div class="stat-label">Stok Habis</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Stok Saat Ini</th>
                <th>Stok Minimum</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['products'] as $product)
            <tr>
                <td>{{ $product->code }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                <td>{{ $product->current_stock }}</td>
                <td>{{ $product->minimum_stock }}</td>
                <td>
                    @if($product->current_stock <= 0)
                        <span class="status-out">Habis</span>
                        @elseif($product->current_stock <= $product->minimum_stock)
                            <span class="status-low">Menipis</span>
                            @else
                            <span class="status-normal">Normal</span>
                            @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($reportType === 'sales')
    <div class="stats-section">
        <div class="stat-item">
            <div class="stat-value">{{ $reportData['total_sales'] }}</div>
            <div class="stat-label">Total Transaksi</div>
        </div>
        <div class="stat-item" style="width: 66.66%;">
            <div class="stat-value">Rp {{ number_format($reportData['grand_total'], 0, ',', '.') }}</div>
            <div class="stat-label">Total Penjualan</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. SO</th>
                <th>Customer</th>
                <th>Tanggal</th>
                <th>Subtotal</th>
                <th>Diskon</th>
                <th>Pajak</th>
                <th>Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['sales'] as $sale)
            @php
                $discountAmt = $sale->total_amount * ($sale->discount / 100);
                $afterDiscount = $sale->total_amount - $discountAmt;
                $taxAmt = $afterDiscount * ($sale->tax / 100);
            @endphp
            <tr>
                <td>{{ $sale->so_number }}</td>
                <td>{{ $sale->customer?->name ?? 'N/A' }}</td>
                <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                <td>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                <td>{{ $sale->discount }}% (Rp {{ number_format($discountAmt, 0, ',', '.') }})</td>
                <td>{{ $sale->tax }}% (Rp {{ number_format($taxAmt, 0, ',', '.') }})</td>
                <td>Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($reportType === 'purchase')
    <div class="stats-section">
        <div class="stat-item">
            <div class="stat-value">{{ $reportData['total_purchases'] }}</div>
            <div class="stat-label">Total Transaksi</div>
        </div>
        <div class="stat-item" style="width: 66.66%;">
            <div class="stat-value">Rp {{ number_format($reportData['total_amount'], 0, ',', '.') }}</div>
            <div class="stat-label">Total Pembelian</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. PO</th>
                <th>Supplier</th>
                <th>Tanggal</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['purchases'] as $purchase)
            <tr>
                <td>{{ $purchase->po_number }}</td>
                <td>{{ $purchase->supplier?->name ?? 'N/A' }}</td>
                <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                <td>Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($reportType === 'stock_movement')
    <div class="stats-section">
        <div class="stat-item">
            <div class="stat-value">{{ $reportData['total_movements'] }}</div>
            <div class="stat-label">Total Pergerakan</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $reportData['total_in'] }}</div>
            <div class="stat-label">Stok Masuk</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $reportData['total_out'] }}</div>
            <div class="stat-label">Stok Keluar</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Tipe</th>
                <th>Qty</th>
                <th>Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['movements'] as $movement)
            <tr>
                <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $movement->product->name }}</td>
                <td>
                    @if($movement->type === 'in')
                    <span class="type-in">Masuk</span>
                    @else
                    <span class="type-out">Keluar</span>
                    @endif
                </td>
                <td>{{ $movement->quantity }}</td>
                <td>{{ $movement->current_stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($reportType === 'tax')
    @php
        $diff = $reportData['tax_difference'];
    @endphp

    {{-- ── HEADER TITLE ── --}}
    <div style="text-align:center; margin-bottom:16px;">
        <div style="font-size:14px; font-weight:bold; color:#1d4ed8; text-transform:uppercase; letter-spacing:1px;">
            LAPORAN PAJAK PERTAMBAHAN NILAI (PPN)
        </div>
        <div style="font-size:10px; color:#666; margin-top:4px;">
            Periode: {{ \Carbon\Carbon::parse($reportData['period']['start'])->format('d/m/Y') }}
            s/d {{ \Carbon\Carbon::parse($reportData['period']['end'])->format('d/m/Y') }}
        </div>
    </div>

    {{-- ── SUMMARY BOX ── --}}
    <div class="stats-section" style="margin-bottom:20px;">
        <div class="stat-item" style="background:#fef2f2; border:1px solid #fecaca;">
            <div class="stat-value" style="color:#991b1b;">
                Rp {{ number_format($reportData['total_tax_out'], 0, ',', '.') }}
            </div>
            <div class="stat-label" style="color:#dc2626;">PPN Keluaran (Penjualan)</div>
        </div>
        <div class="stat-item" style="background:#f0fdf4; border:1px solid #bbf7d0;">
            <div class="stat-value" style="color:#166534;">
                Rp {{ number_format($reportData['total_tax_in'], 0, ',', '.') }}
            </div>
            <div class="stat-label" style="color:#16a34a;">PPN Masukan (Pembelian)</div>
        </div>
        <div class="stat-item" style="background:{{ $diff >= 0 ? '#fff7ed' : '#eff6ff' }}; border:1px solid {{ $diff >= 0 ? '#fed7aa' : '#bfdbfe' }};">
            <div class="stat-value" style="color:{{ $diff >= 0 ? '#c2410c' : '#1d4ed8' }};">
                Rp {{ number_format(abs($diff), 0, ',', '.') }}
            </div>
            <div class="stat-label" style="color:{{ $diff >= 0 ? '#ea580c' : '#2563eb' }};">
                {{ $diff >= 0 ? 'Kurang Bayar (KB)' : 'Lebih Bayar (LB)' }}
            </div>
        </div>
    </div>

    {{-- ── BAGIAN 1: PPN KELUARAN ── --}}
    <div style="margin-bottom:4px; margin-top:16px;">
        <span style="display:inline-block; width:10px; height:10px; background:#dc2626; border-radius:50%; margin-right:6px;"></span>
        <strong style="font-size:11px; color:#991b1b;">PPN KELUARAN — Penjualan ke Customer</strong>
    </div>

    @if($reportData['sales']->count() > 0)
    <table>
        <thead>
            <tr style="background:#fef2f2;">
                <th style="width:12%;">No. SO</th>
                <th style="width:10%;">Tanggal</th>
                <th style="width:22%;">Pelanggan</th>
                <th style="width:16%; text-align:right;">DPP (Rp)</th>
                <th style="width:8%; text-align:center;">Tarif</th>
                <th style="width:16%; text-align:right;">Nilai PPN (Rp)</th>
                <th style="width:16%; text-align:right;">Total Tagihan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['sales'] as $row)
            <tr>
                <td style="font-family:monospace; font-size:9px;">{{ $row['number'] }}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['party'] }}</td>
                <td style="text-align:right;">{{ number_format($row['dpp'], 0, ',', '.') }}</td>
                <td style="text-align:center;">{{ $row['tax_rate'] }}%</td>
                <td style="text-align:right; color:#991b1b; font-weight:bold;">{{ number_format($row['tax_amount'], 0, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($row['grand_total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#fef2f2;">
                <td colspan="5" style="text-align:right; font-weight:bold; font-size:11px;">Total PPN Keluaran</td>
                <td style="text-align:right; font-weight:bold; color:#991b1b; font-size:11px;">
                    Rp {{ number_format($reportData['total_tax_out'], 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @else
    <div style="text-align:center; padding:12px; background:#fef9f9; border:1px solid #fecaca; color:#991b1b; font-size:10px;">
        Tidak ada penjualan dengan PPN pada periode ini.
    </div>
    @endif

    {{-- ── BAGIAN 2: PPN MASUKAN ── --}}
    <div style="margin-bottom:4px; margin-top:20px;">
        <span style="display:inline-block; width:10px; height:10px; background:#16a34a; border-radius:50%; margin-right:6px;"></span>
        <strong style="font-size:11px; color:#166534;">PPN MASUKAN — Pembelian dari Supplier</strong>
    </div>

    @if($reportData['purchases']->count() > 0)
    <table>
        <thead>
            <tr style="background:#f0fdf4;">
                <th style="width:12%;">No. PO</th>
                <th style="width:10%;">Tanggal</th>
                <th style="width:22%;">Supplier</th>
                <th style="width:16%; text-align:right;">DPP (Rp)</th>
                <th style="width:8%; text-align:center;">Tarif</th>
                <th style="width:16%; text-align:right;">Nilai PPN (Rp)</th>
                <th style="width:16%; text-align:right;">Total Tagihan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['purchases'] as $row)
            <tr>
                <td style="font-family:monospace; font-size:9px;">{{ $row['number'] }}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['party'] }}</td>
                <td style="text-align:right;">{{ number_format($row['dpp'], 0, ',', '.') }}</td>
                <td style="text-align:center;">{{ $row['tax_rate'] }}%</td>
                <td style="text-align:right; color:#166534; font-weight:bold;">{{ number_format($row['tax_amount'], 0, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($row['grand_total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#f0fdf4;">
                <td colspan="5" style="text-align:right; font-weight:bold; font-size:11px;">Total PPN Masukan</td>
                <td style="text-align:right; font-weight:bold; color:#166534; font-size:11px;">
                    Rp {{ number_format($reportData['total_tax_in'], 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @else
    <div style="text-align:center; padding:12px; background:#f0fdf9; border:1px solid #bbf7d0; color:#166534; font-size:10px;">
        Tidak ada pembelian dengan PPN pada periode ini.
    </div>
    @endif

    {{-- ── REKAP AKHIR ── --}}
    <div style="margin-top:24px; border-top:2px solid #333; padding-top:12px;">
        <strong style="font-size:11px;">REKAP AKHIR PPN</strong>
        <table style="width:280px; float:right; margin-top:8px; border:1px solid #ddd;">
            <tbody>
                <tr>
                    <td style="padding:6px 10px; background:#fef2f2;">Total PPN Keluaran</td>
                    <td style="padding:6px 10px; text-align:right; background:#fef2f2; color:#991b1b; font-weight:bold;">
                        Rp {{ number_format($reportData['total_tax_out'], 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:6px 10px; background:#f0fdf4;">Total PPN Masukan</td>
                    <td style="padding:6px 10px; text-align:right; background:#f0fdf4; color:#166534; font-weight:bold;">
                        Rp {{ number_format($reportData['total_tax_in'], 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 10px; background:{{ $diff >= 0 ? '#fff7ed' : '#eff6ff' }}; font-weight:bold;">
                        {{ $diff >= 0 ? 'PPN Kurang Bayar (KB)' : 'PPN Lebih Bayar (LB)' }}
                    </td>
                    <td style="padding:8px 10px; text-align:right; background:{{ $diff >= 0 ? '#fff7ed' : '#eff6ff' }}; color:{{ $diff >= 0 ? '#c2410c' : '#1d4ed8' }}; font-weight:bold; font-size:13px;">
                        Rp {{ number_format(abs($diff), 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="clear:both;"></div>
        <p style="font-size:8px; color:#999; margin-top:10px;">
            * Laporan ini merupakan rekap internal untuk keperluan pencatatan.
              Pelaporan resmi PPN tetap dilakukan melalui e-Faktur Direktorat Jenderal Pajak.
        </p>
    </div>
    @endif

    <div class="footer">
        <div class="footer-left">
            <strong>Dicetak oleh:</strong> {{ auth()->user()?->name ?? 'System' }}<br>
            <strong>Tanggal:</strong> {{ now()->format('d/m/Y H:i') }}
        </div>
        <div class="footer-right">
            <strong>UMKM Kota Depok</strong><br>
            Sistem Inventori UMKM Kota Depok
        </div>
        <div style="clear: both;"></div>
    </div>
</body>

</html>