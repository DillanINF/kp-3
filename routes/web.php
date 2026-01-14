<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemInController;
use App\Http\Controllers\ItemOutController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('role:admin,manager')->name('dashboard');

    Route::get('/reports', [ReportController::class, 'index'])->middleware('role:admin,manager')->name('reports.index');

    Route::get('/reports/pdf', [ReportController::class, 'pdf'])->middleware('role:admin,manager')->name('reports.pdf');

    Route::prefix('invoices')->name('invoices.')->middleware('role:admin,manager')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');

        Route::post('/', [InvoiceController::class, 'store'])->middleware('role:admin')->name('store');

        Route::post('/{invoice}/accept', [InvoiceController::class, 'accept'])->middleware('role:admin')->name('accept');

        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->middleware('role:admin')->name('destroy');

        Route::get('/by-no/{invoiceNo}/input-po', [InvoiceController::class, 'inputPoByNo'])->middleware('role:admin')->name('input_po_by_no');

        Route::get('/{invoice}/input-po', [InvoiceController::class, 'inputPo'])->middleware('role:admin')->name('input_po');
        Route::post('/{invoice}/input-po', [InvoiceController::class, 'storePo'])->middleware('role:admin')->name('input_po.store');

        Route::get('/po-belum-terkirim', [InvoiceController::class, 'poPending'])->name('po_pending');

        Route::post('/po-belum-terkirim/{pending}/fulfill', [InvoiceController::class, 'fulfillPoPending'])
            ->middleware('role:admin')
            ->name('po_pending.fulfill');

        Route::put('/po-belum-terkirim/{pending}', [InvoiceController::class, 'updatePoPending'])
            ->middleware('role:admin')
            ->name('po_pending.update');

        Route::delete('/po-belum-terkirim/{pending}', [InvoiceController::class, 'destroyPoPending'])
            ->middleware('role:admin')
            ->name('po_pending.destroy');
    });

    Route::prefix('masters')->name('masters.')->middleware('role:admin,manager')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
        Route::post('/customers', [CustomerController::class, 'store'])->middleware('role:admin')->name('customers.store');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->middleware('role:admin')->name('customers.update');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->middleware('role:admin')->name('customers.destroy');

        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
        Route::post('/suppliers', [SupplierController::class, 'store'])->middleware('role:admin')->name('suppliers.store');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->middleware('role:admin')->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->middleware('role:admin')->name('suppliers.destroy');

        Route::post('/suppliers/{supplier}/items', [SupplierController::class, 'storeItem'])->middleware('role:admin')->name('suppliers.items.store');
        Route::post('/suppliers/{supplier}/items/new', [SupplierController::class, 'storeNewItem'])->middleware('role:admin')->name('suppliers.items.store_new');
        Route::put('/suppliers/{supplier}/items/{supplierItem}', [SupplierController::class, 'updateItem'])->middleware('role:admin')->name('suppliers.items.update');
        Route::delete('/suppliers/{supplier}/items/{supplierItem}', [SupplierController::class, 'destroyItem'])->middleware('role:admin')->name('suppliers.items.destroy');

        Route::get('/items', [ItemController::class, 'index'])->name('items');
        Route::get('/items-supplier', [SupplierRequestController::class, 'index'])->name('items_supplier');
        Route::post('/items-supplier', [SupplierRequestController::class, 'store'])->middleware('role:admin')->name('items_supplier.store');
        Route::put('/items-supplier/{supplierRequest}', [SupplierRequestController::class, 'update'])->middleware('role:admin')->name('items_supplier.update');
        Route::post('/items-supplier/{supplierRequest}/send', [SupplierRequestController::class, 'send'])->middleware('role:admin')->name('items_supplier.send');
        Route::post('/items-supplier/{supplierRequest}/accept', [SupplierRequestController::class, 'accept'])->middleware('role:admin')->name('items_supplier.accept');
        Route::delete('/items-supplier/{supplierRequest}', [SupplierRequestController::class, 'destroy'])->middleware('role:admin')->name('items_supplier.destroy');
        Route::post('/items', [ItemController::class, 'store'])->middleware('role:admin')->name('items.store');
        Route::put('/items/{item}', [ItemController::class, 'update'])->middleware('role:admin')->name('items.update');
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->middleware('role:admin')->name('items.destroy');

        Route::get('/items-in', [ItemInController::class, 'index'])->name('items_in');
        Route::post('/items-in', [ItemInController::class, 'store'])->middleware('role:admin')->name('items_in.store');
        Route::put('/items-in/{itemIn}', [ItemInController::class, 'update'])->middleware('role:admin')->name('items_in.update');
        Route::delete('/items-in/{itemIn}', [ItemInController::class, 'destroy'])->middleware('role:admin')->name('items_in.destroy');

        Route::get('/items-out', [ItemOutController::class, 'index'])->name('items_out');
        Route::post('/items-out', [ItemOutController::class, 'store'])->middleware('role:admin')->name('items_out.store');
        Route::put('/items-out/{itemOut}', [ItemOutController::class, 'update'])->middleware('role:admin')->name('items_out.update');
        Route::delete('/items-out/{itemOut}', [ItemOutController::class, 'destroy'])->middleware('role:admin')->name('items_out.destroy');
    });

    Route::get('/settings', [SettingsController::class, 'index'])->middleware('role:admin,manager')->name('settings');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->middleware('role:admin,manager')->name('settings.profile.update');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->middleware('role:admin,manager')->name('settings.password.update');
    Route::post('/settings/manager', [SettingsController::class, 'storeManager'])->middleware('role:admin')->name('settings.manager.store');
});
