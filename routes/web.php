<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;

// Halaman utama marketplace
Route::get('/', [MarketplaceController::class, 'index'])
    ->name('home');

// Halaman detail katering
Route::get('/katering/{merchant}', [MarketplaceController::class, 'show'])
    ->name('marketplace.show');

// Register dan login
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Fitur khusus merchant
Route::middleware(['auth', 'role:merchant'])
    ->prefix('merchant')
    ->name('merchant.')
    ->group(function () {

        Route::get('/dashboard', [MerchantController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/profile', [MerchantController::class, 'editProfile'])
            ->name('profile.edit');

        Route::put('/profile', [MerchantController::class, 'updateProfile'])
            ->name('profile.update');

        Route::post('/menus', [MerchantController::class, 'storeMenu'])
            ->name('menus.store');

        Route::put('/menus/{menu}', [MerchantController::class, 'updateMenu'])
            ->name('menus.update');

        Route::delete('/menus/{menu}', [MerchantController::class, 'deleteMenu'])
            ->name('menus.delete');

        Route::patch('/orders/{order}/status', [MerchantController::class, 'updateOrderStatus'])
            ->name('orders.status');
    });

// Fitur khusus customer
Route::middleware(['auth', 'role:customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {

        Route::get('/dashboard', [MarketplaceController::class, 'customerDashboard'])
            ->name('dashboard');

        Route::post('/orders', [OrderController::class, 'store'])
            ->name('orders.store');

        Route::get('/orders/{order}', [OrderController::class, 'show'])
            ->name('orders.show');

        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])
            ->name('orders.edit');

        Route::put('/orders/{order}', [OrderController::class, 'update'])
            ->name('orders.update');

        Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])
            ->name('orders.cancel');
    });

// Fitur melihat invoice
Route::middleware('auth')->group(function () {
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])
        ->name('invoices.show');
});
