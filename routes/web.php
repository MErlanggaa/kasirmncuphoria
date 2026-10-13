<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\PublicTicketController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('kasir.pos');
    }
    return redirect()->route('login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Public Customer E-Ticket for Pre-Orders
Route::get('/ticket/{token}', [PublicTicketController::class, 'show'])->name('ticket.show');

// Kasir (Cashier) Routes
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/', [KasirController::class, 'pos'])->name('pos');
    Route::post('/transaction', [KasirController::class, 'storeTransaction'])->name('transaction.store');
    Route::get('/qris/dynamic', [KasirController::class, 'generateDynamicQris'])->name('qris.dynamic');
    Route::get('/pickup', [KasirController::class, 'pickupScanner'])->name('pickup');
    Route::get('/pickup/verify/{token}', [KasirController::class, 'verifyPoToken'])->name('pickup.verify');
    Route::post('/pickup/confirm/{id}', [KasirController::class, 'confirmPickup'])->name('pickup.confirm');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Products / Menu Management
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('products.delete');

    // Cashiers Account Management
    Route::get('/cashiers', [AdminController::class, 'cashiers'])->name('cashiers');
    Route::post('/cashiers', [AdminController::class, 'storeCashier'])->name('cashiers.store');
    Route::put('/cashiers/{user}', [AdminController::class, 'updateCashier'])->name('cashiers.update');
    Route::post('/cashiers/{user}/toggle', [AdminController::class, 'toggleCashierStatus'])->name('cashiers.toggle');

    // Transactions History & Reports
    Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'saveSettings'])->name('settings.save');

    // Pickup scanner also accessible by admin
    Route::get('/pickup', [KasirController::class, 'pickupScanner'])->name('pickup');
    Route::get('/pickup/verify/{token}', [KasirController::class, 'verifyPoToken'])->name('pickup.verify');
    Route::post('/pickup/confirm/{id}', [KasirController::class, 'confirmPickup'])->name('pickup.confirm');
});
