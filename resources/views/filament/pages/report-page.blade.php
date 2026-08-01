<!-- resources/views/filament/pages/report-page.blade.php -->
<x-filament-panels::page>
    <style>
        /* Dark mode support */
        .dark .report-card {
            background-color: rgb(31 41 55) !important;
            border-color: rgb(55 65 81) !important;
            color: rgb(243 244 246) !important;
        }

        .dark .report-card h3 {
            color: rgb(243 244 246) !important;
        }

        .dark .report-card p {
            color: rgb(156 163 175) !important;
        }

        .report-card {
            margin-bottom: 3rem !important;
        }

        .dark .report-table {
            background-color: rgb(31 41 55) !important;
            border-color: rgb(55 65 81) !important;
        }

        .dark .report-table thead {
            background-color: rgb(55 65 81) !important;
        }

        .dark .report-table th {
            color: rgb(209 213 219) !important;
            border-color: rgb(75 85 99) !important;
        }

        .dark .report-table td {
            color: rgb(243 244 246) !important;
            border-color: rgb(75 85 99) !important;
        }

        .dark .report-table tbody tr:nth-child(even) {
            background-color: rgb(55 65 81) !important;
        }

        .dark .stat-card {
            background-color: rgb(31 41 55) !important;
            border-color: rgb(55 65 81) !important;
        }

        .dark .empty-state {
            color: rgb(156 163 175) !important;
        }

        .dark .empty-state h3 {
            color: rgb(243 244 246) !important;
        }

        /* Improved spacing */
        .form-spacing {
            margin-bottom: 2rem !important;
        }

        .generate-button {
            margin-top: 1.5rem !important;
        }

        /* Sticky export bar */
        .export-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
            padding: 0.875rem 2rem;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.08);
        }

        .dark .export-bar {
            background: #1f2937;
            border-top-color: #374151;
        }

        /* Push content so last rows aren't hidden behind bar */
        .report-content-wrap {
            padding-bottom: 80px;
        }

        /* Export buttons styling - FIX untuk light dan dark theme */
        .export-button {
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            font-weight: 500 !important;
            padding: 0.875rem 2.5rem !important;
            border-radius: 0.625rem !important;
            border: none !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            font-size: 0.95rem !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
        }

        .export-button:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-1px) !important;
        }

        /* Light theme - PDF Button (Soft Red/Pink) */
        .export-pdf {
            background-color: #fca5a5 !important;
            color: #7f1d1d !important;
        }

        .export-pdf:hover {
            background-color: #f87171 !important;
        }

        /* Light theme - Excel Button (Soft Green) */
        .export-excel {
            background-color: #86efac !important;
            color: #166534 !important;
        }

        .export-excel:hover {
            background-color: #4ade80 !important;
        }

        /* Dark theme - PDF Button */
        .dark .export-pdf {
            background-color: #fca5a5 !important;
            color: #7f1d1d !important;
        }

        .dark .export-pdf:hover {
            background-color: #f87171 !important;
        }

        /* Dark theme - Excel Button */
        .dark .export-excel {
            background-color: #86efac !important;
            color: #166534 !important;
        }

        .dark .export-excel:hover {
            background-color: #4ade80 !important;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                font-size: 12px;
                line-height: 1.4;
            }
        }
    </style>

    <div class="space-y-6 report-content-wrap">
        <!-- Form Section -->
        <div class="form-spacing">
            <form wire:submit="generateReport" class="no-print">
                {{ $this->form }}

                <div class="flex justify-end generate-button">
                    <x-filament::button type="submit" size="lg">
                        Generate Laporan
                    </x-filament::button>
                </div>
            </form>
        </div>

        <!-- Report Results -->
        @if($this->reportData)
        <div class="mt-8">
            <!-- Stock Report -->
            @if($this->reportData['type'] === 'stock')
            <div class="report-card bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Laporan Stok</h3>

                @if($this->reportData['total_products'] > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded">
                        <div class="text-2xl font-bold text-blue-800 dark:text-blue-300">{{ $this->reportData['total_products'] }}</div>
                        <div class="text-blue-600 dark:text-blue-400">Total Produk</div>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded">
                        <div class="text-2xl font-bold text-yellow-800 dark:text-yellow-300">{{ $this->reportData['low_stock_products']->count() }}</div>
                        <div class="text-yellow-600 dark:text-yellow-400">Stok Menipis</div>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded">
                        <div class="text-2xl font-bold text-red-800 dark:text-red-300">{{ $this->reportData['out_of_stock_products']->count() }}</div>
                        <div class="text-red-600 dark:text-red-400">Stok Habis</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="report-table min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nama Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Stok Saat Ini</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Stok Minimum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->reportData['products'] as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $product->code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $product->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $product->category->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $product->current_stock }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $product->minimum_stock }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if($product->current_stock <= 0)
                                        <span class="text-red-600 dark:text-red-400">Habis</span>
                                        @elseif($product->current_stock <= $product->minimum_stock)
                                            <span class="text-yellow-600 dark:text-yellow-400">Menipis</span>
                                            @else
                                            <span class="text-green-600 dark:text-green-400">Normal</span>
                                            @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state text-center py-12">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                        <svg class="h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Belum Ada Data Produk</h3>
                    <p class="text-gray-500 dark:text-gray-400">Sistem belum memiliki data produk untuk ditampilkan.</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Sales Report -->
            @if($this->reportData['type'] === 'sales')
            <div class="report-card bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Laporan Penjualan</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Periode: {{ \Carbon\Carbon::parse($this->reportData['period']['start'])->format('d/m/Y') }} -
                    {{ \Carbon\Carbon::parse($this->reportData['period']['end'])->format('d/m/Y') }}
                </p>

                @if($this->reportData['total_sales'] > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded">
                        <div class="text-2xl font-bold text-blue-800 dark:text-blue-300">{{ $this->reportData['total_sales'] }}</div>
                        <div class="text-blue-600 dark:text-blue-400">Total Transaksi</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded">
                        <div class="text-2xl font-bold text-green-800 dark:text-green-300">Rp {{ number_format($this->reportData['grand_total'], 0, ',', '.') }}</div>
                        <div class="text-green-600 dark:text-green-400">Total Penjualan</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="report-table min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">No. SO</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Subtotal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Diskon</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pajak</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Grand Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->reportData['sales'] as $sale)
                            @php
                                $discountAmt   = $sale->total_amount * ($sale->discount / 100);
                                $afterDiscount = $sale->total_amount - $discountAmt;
                                $taxAmt        = $afterDiscount * ($sale->tax / 100);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $sale->so_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $sale->customer?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $sale->sale_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $sale->discount }}% (Rp {{ number_format($discountAmt, 0, ',', '.') }})</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $sale->tax }}% (Rp {{ number_format($taxAmt, 0, ',', '.') }})</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state text-center py-12">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Tidak Ada Data Penjualan</h3>
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada transaksi penjualan pada periode tersebut.</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Purchase Report -->
            @if($this->reportData['type'] === 'purchase')
            <div class="report-card bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Laporan Pembelian</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Periode: {{ \Carbon\Carbon::parse($this->reportData['period']['start'])->format('d/m/Y') }} -
                    {{ \Carbon\Carbon::parse($this->reportData['period']['end'])->format('d/m/Y') }}
                </p>

                @if($this->reportData['total_purchases'] > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded">
                        <div class="text-2xl font-bold text-blue-800 dark:text-blue-300">{{ $this->reportData['total_purchases'] }}</div>
                        <div class="text-blue-600 dark:text-blue-400">Total Transaksi</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded">
                        <div class="text-2xl font-bold text-green-800 dark:text-green-300">Rp {{ number_format($this->reportData['total_amount'], 0, ',', '.') }}</div>
                        <div class="text-green-600 dark:text-green-400">Total Pembelian</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="report-table min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">No. PO</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Supplier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->reportData['purchases'] as $purchase)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $purchase->po_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $purchase->supplier?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state text-center py-12">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Tidak Ada Data Pembelian</h3>
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada transaksi pembelian pada periode tersebut.</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Stock Movement Report -->
            @if($this->reportData['type'] === 'stock_movement')
            <div class="report-card bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Laporan Pergerakan Stok</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Periode: {{ \Carbon\Carbon::parse($this->reportData['period']['start'])->format('d/m/Y') }} -
                    {{ \Carbon\Carbon::parse($this->reportData['period']['end'])->format('d/m/Y') }}
                </p>

                @if($this->reportData['total_movements'] > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded">
                        <div class="text-2xl font-bold text-blue-800 dark:text-blue-300">{{ $this->reportData['total_movements'] }}</div>
                        <div class="text-blue-600 dark:text-blue-400">Total Pergerakan</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded">
                        <div class="text-2xl font-bold text-green-800 dark:text-green-300">{{ $this->reportData['total_in'] }}</div>
                        <div class="text-green-600 dark:text-green-400">Stok Masuk</div>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded">
                        <div class="text-2xl font-bold text-red-800 dark:text-red-300">{{ $this->reportData['total_out'] }}</div>
                        <div class="text-red-600 dark:text-red-400">Stok Keluar</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="report-table min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Stok Akhir</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->reportData['movements'] as $movement)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $movement->product->name }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if($movement->type === 'in')
                                    <span class="text-green-600 dark:text-green-400">Masuk</span>
                                    @else
                                    <span class="text-red-600 dark:text-red-400">Keluar</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $movement->quantity }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $movement->current_stock }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state text-center py-12">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Tidak Ada Pergerakan Stok</h3>
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada pergerakan stok pada periode tersebut.</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Tax / PPN Report -->
            @if($this->reportData['type'] === 'tax')
            <div class="report-card bg-white dark:bg-gray-800 shadow rounded-lg p-6">

                {{-- Header --}}
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-1">Laporan PPN</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Periode: {{ \Carbon\Carbon::parse($this->reportData['period']['start'])->format('d/m/Y') }} —
                    {{ \Carbon\Carbon::parse($this->reportData['period']['end'])->format('d/m/Y') }}
                </p>

                {{-- Summary Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <div class="text-xs font-semibold text-red-500 dark:text-red-400 uppercase tracking-wide mb-1">
                            PPN Keluaran
                        </div>
                        <div class="text-xs text-red-400 dark:text-red-500 mb-2">Dari Penjualan ke Customer</div>
                        <div class="text-2xl font-bold text-red-700 dark:text-red-300">
                            Rp {{ number_format($this->reportData['total_tax_out'], 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-red-500 dark:text-red-400 mt-1">
                            {{ $this->reportData['sales']->count() }} transaksi
                        </div>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                        <div class="text-xs font-semibold text-green-500 dark:text-green-400 uppercase tracking-wide mb-1">
                            PPN Masukan
                        </div>
                        <div class="text-xs text-green-400 dark:text-green-500 mb-2">Dari Pembelian ke Supplier</div>
                        <div class="text-2xl font-bold text-green-700 dark:text-green-300">
                            Rp {{ number_format($this->reportData['total_tax_in'], 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-green-500 dark:text-green-400 mt-1">
                            {{ $this->reportData['purchases']->count() }} transaksi
                        </div>
                    </div>

                    @php $diff = $this->reportData['tax_difference']; @endphp
                    <div class="{{ $diff >= 0 ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800' : 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' }} border rounded-lg p-4">
                        <div class="text-xs font-semibold {{ $diff >= 0 ? 'text-orange-500 dark:text-orange-400' : 'text-blue-500 dark:text-blue-400' }} uppercase tracking-wide mb-1">
                            {{ $diff >= 0 ? 'Kurang Bayar (KB)' : 'Lebih Bayar (LB)' }}
                        </div>
                        <div class="text-xs {{ $diff >= 0 ? 'text-orange-400 dark:text-orange-500' : 'text-blue-400 dark:text-blue-500' }} mb-2">
                            PPN Keluaran — PPN Masukan
                        </div>
                        <div class="text-2xl font-bold {{ $diff >= 0 ? 'text-orange-700 dark:text-orange-300' : 'text-blue-700 dark:text-blue-300' }}">
                            Rp {{ number_format(abs($diff), 0, ',', '.') }}
                        </div>
                        <div class="text-xs {{ $diff >= 0 ? 'text-orange-500' : 'text-blue-500' }} mt-1">
                            {{ $diff >= 0 ? 'PPN yang harus disetorkan' : 'PPN lebih bayar / dapat dikreditkan' }}
                        </div>
                    </div>
                </div>

                {{-- BAGIAN 1: PPN Keluaran --}}
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <h4 class="text-base font-semibold text-gray-800 dark:text-gray-200">
                            PPN Keluaran — Penjualan ke Customer
                        </h4>
                    </div>

                    @if($this->reportData['sales']->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="report-table min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-red-50 dark:bg-red-900/30">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">No. SO</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Pelanggan</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">DPP (Rp)</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Tarif</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Nilai PPN (Rp)</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Total Tagihan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($this->reportData['sales'] as $row)
                                <tr class="hover:bg-red-50/50 dark:hover:bg-red-900/10">
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-mono text-xs">{{ $row['number'] }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $row['date'] }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $row['party'] }}</td>
                                    <td class="px-4 py-3 text-right text-gray-900 dark:text-gray-100">{{ number_format($row['dpp'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300">
                                            {{ $row['tax_rate'] }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-red-700 dark:text-red-300">{{ number_format($row['tax_amount'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-gray-900 dark:text-gray-100">{{ number_format($row['grand_total'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-red-50 dark:bg-red-900/20">
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-right text-sm font-bold text-gray-700 dark:text-gray-300">
                                        Total PPN Keluaran
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-bold text-red-700 dark:text-red-300">
                                        Rp {{ number_format($this->reportData['total_tax_out'], 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Tidak ada penjualan dengan PPN pada periode ini.</p>
                    </div>
                    @endif
                </div>

                {{-- BAGIAN 2: PPN Masukan --}}
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        <h4 class="text-base font-semibold text-gray-800 dark:text-gray-200">
                            PPN Masukan — Pembelian dari Supplier
                        </h4>
                    </div>

                    @if($this->reportData['purchases']->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="report-table min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-green-50 dark:bg-green-900/30">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">No. PO</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Supplier</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">DPP (Rp)</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Tarif</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Nilai PPN (Rp)</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Total Tagihan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($this->reportData['purchases'] as $row)
                                <tr class="hover:bg-green-50/50 dark:hover:bg-green-900/10">
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-mono text-xs">{{ $row['number'] }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $row['date'] }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $row['party'] }}</td>
                                    <td class="px-4 py-3 text-right text-gray-900 dark:text-gray-100">{{ number_format($row['dpp'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300">
                                            {{ $row['tax_rate'] }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-green-700 dark:text-green-300">{{ number_format($row['tax_amount'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-gray-900 dark:text-gray-100">{{ number_format($row['grand_total'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-green-50 dark:bg-green-900/20">
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-right text-sm font-bold text-gray-700 dark:text-gray-300">
                                        Total PPN Masukan
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-bold text-green-700 dark:text-green-300">
                                        Rp {{ number_format($this->reportData['total_tax_in'], 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-6 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Tidak ada pembelian dengan PPN pada periode ini.</p>
                    </div>
                    @endif
                </div>

                {{-- BAGIAN 3: Rekap Akhir --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h4 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-4">Rekap PPN Periode Ini</h4>
                    <div class="max-w-sm ml-auto">
                        <table class="w-full text-sm">
                            <tbody>
                                <tr>
                                    <td class="py-2 text-gray-600 dark:text-gray-400">Total PPN Keluaran</td>
                                    <td class="py-2 text-right font-semibold text-red-700 dark:text-red-300">
                                        Rp {{ number_format($this->reportData['total_tax_out'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600 dark:text-gray-400">Total PPN Masukan</td>
                                    <td class="py-2 text-right font-semibold text-green-700 dark:text-green-300">
                                        Rp {{ number_format($this->reportData['total_tax_in'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="border-t border-gray-300 dark:border-gray-600">
                                    <td class="py-3 font-bold text-gray-900 dark:text-gray-100">
                                        {{ $diff >= 0 ? 'PPN Kurang Bayar (KB)' : 'PPN Lebih Bayar (LB)' }}
                                    </td>
                                    <td class="py-3 text-right text-lg font-bold {{ $diff >= 0 ? 'text-orange-600 dark:text-orange-400' : 'text-blue-600 dark:text-blue-400' }}">
                                        Rp {{ number_format(abs($this->reportData['tax_difference']), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">
                            * Laporan ini merupakan rekap internal. Pelaporan resmi PPN tetap melalui e-Faktur DJP.
                        </p>
                    </div>
                </div>

            </div>
            @endif

        </div>
        @endif
    </div>

    {{-- Sticky export bar — only visible after report is generated --}}
    @if($this->reportData)
    <div class="export-bar no-print">
        <button onclick="exportToPDF()" class="export-button export-pdf">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
            </svg>
            <span>Export PDF</span>
        </button>
        <button onclick="exportToExcel()" class="export-button export-excel">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Export Excel</span>
        </button>
    </div>
    @endif

    <script>
        function buildExportForm(action, target) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = action;
            if (target) form.target = target;

            const addField = (name, value) => {
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = name;
                input.value = value ?? '';
                form.appendChild(input);
            };

            addField('_token', '{{ csrf_token() }}');

            // Read each field explicitly from Livewire to avoid proxy enumeration issues
            const wireData = @this.data;
            addField('report_type', wireData.report_type);
            addField('start_date',  wireData.start_date);
            addField('end_date',    wireData.end_date);

            document.body.appendChild(form);
            form.submit();
            // Remove after a tick to ensure submit is queued
            setTimeout(() => document.body.removeChild(form), 100);
        }

        function exportToPDF()   { buildExportForm('{{ route("reports.export.pdf") }}',   '_blank'); }
        function exportToExcel() { buildExportForm('{{ route("reports.export.excel") }}',  null); }
    </script>
</x-filament-panels::page>