<?php

use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportExportController;

// Redirect homepage ke admin login
Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/purchase-order/{purchaseOrder}/invoice', function (PurchaseOrder $purchaseOrder) {
    $purchaseOrder->load(['supplier', 'items.product']);
    return view('filament.pages.purchase-invoice', compact('purchaseOrder'));
})->name('purchase-order.invoice')->middleware('auth');

Route::get('/sales-order/{salesOrder}/invoice', function (SalesOrder $salesOrder) {
    $salesOrder->load(['customer', 'items.product']);
    return view('filament.pages.sales-invoice', compact('salesOrder'));
})->name('sales-order.invoice')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::post('/reports/export/pdf', [ReportExportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::post('/reports/export/excel', [ReportExportController::class, 'exportExcel'])->name('reports.export.excel');
});
