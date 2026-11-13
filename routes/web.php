<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceReportController;
use App\Http\Controllers\AuditReportController;

Route::middleware(['auth', 'verified'])->group(function () {
    // =========================================================================
    // Dashboard & Profile
    // =========================================================================
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('profile', 'profile.index')->name('profile.index');

    // =========================================================================
    // Master Data
    // =========================================================================
    Route::prefix('master')->group(function () {
        Route::view('customers', 'customers.index')->name('customers.index');
        Route::view('suppliers', 'suppliers.index')->name('suppliers.index');
        Route::view('categories', 'categories.index')->name('categories.index');
        Route::view('units', 'units.index')->name('units.index');
        Route::view('products', 'products.index')->name('products.index');
        Route::view('low-stock', 'products.low-in-stock-only')->name('low-stock.index');
    });

    // =========================================================================
    // WMS Operations: Stock In & Stock Out (GudangIn Central Inventory)
    // =========================================================================

    // Stock In (Barang Masuk)
    Route::get('stock-in', [StockInController::class, 'create'])->name('stock-in.create');
    Route::post('stock-in', [StockInController::class, 'store'])->name('stock-in.store');
    Route::get('stock-in/history', [StockInController::class, 'index'])->name('stock-in.index');
    Route::get('stock-in/{purchase}', [StockInController::class, 'show'])->name('stock-in.show');

    // Stock Out (Barang Keluar)
    Route::get('stock-out', [StockOutController::class, 'create'])->name('stock-out.create');
    Route::post('stock-out', [StockOutController::class, 'store'])->name('stock-out.store');
    Route::get('stock-out/history', [StockOutController::class, 'index'])->name('stock-out.index');
    Route::get('stock-out/{sale}', [StockOutController::class, 'show'])->name('stock-out.show');
    Route::get('stock-out/{sale}/print', [StockOutController::class, 'print'])->name('stock-out.print');

    // Legacy CRM Route Redirects & Fallbacks
    Route::redirect('purchases', '/stock-in')->name('purchases.index');
    Route::redirect('purchases/create', '/stock-in')->name('purchases.create');
    Route::post('purchases', [StockInController::class, 'store'])->name('purchases.store');
    Route::get('purchases/{purchase}', [StockInController::class, 'show'])->name('purchases.show');

    Route::redirect('sales', '/stock-out')->name('sales.index');
    Route::redirect('sales/create', '/stock-out')->name('sales.create');
    Route::post('sales', [StockOutController::class, 'store'])->name('sales.store');
    Route::get('sales/{sale}', [StockOutController::class, 'show'])->name('sales.show');
    Route::get('sales/{sale}/print', [StockOutController::class, 'print'])->name('sales.print');

    // =========================================================================
    // Finance
    // =========================================================================
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::view('categories', 'finance-categories.index')->name('categories.index');
        Route::view('transactions', 'finance-transactions.index')->name('transactions.index');
        Route::get('transactions/print/{printId}', [FinanceReportController::class, 'print'])->name('transactions.print');
    });

    // =========================================================================
    // GudangIn Reports & Excel Audit
    // =========================================================================
    Route::get('/reports/audit', [AuditReportController::class, 'index'])->name('reports.audit');
    Route::get('/reports/audit/export', [AuditReportController::class, 'exportExcel'])->name('reports.audit.export');

    // =========================================================================
    // Super Admin Exclusive Area
    // =========================================================================
    Route::middleware(['super_admin'])->group(function () {
        Route::view('users', 'users.index')->name('users.index');
        Route::view('settings', 'settings.index')->name('settings.index');
    });

    // =========================================================================
    // Internal APIs (AJAX)
    // =========================================================================
    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::post('products', [\App\Http\Controllers\Api\ProductController::class, 'search'])->name('products.search');
        Route::post('suppliers', [\App\Http\Controllers\Api\SupplierController::class, 'search'])->name('suppliers.search');
        Route::post('customers', [\App\Http\Controllers\Api\CustomerController::class, 'search'])->name('customers.search');
        Route::post('customers/store', [\App\Http\Controllers\Api\CustomerController::class, 'store'])->name('customers.store');
        Route::post('categories', [\App\Http\Controllers\Api\CategoryController::class, 'search'])->name('categories.search');
        Route::post('units', [\App\Http\Controllers\Api\UnitController::class, 'search'])->name('units.search');
        Route::post('users', [\App\Http\Controllers\Api\UserController::class, 'search'])->name('users.search');
        Route::post('finance-categories', [\App\Http\Controllers\Api\FinanceCategoryController::class, 'search'])->name('finance-categories.search');
    });
});

require __DIR__.'/auth.php';
