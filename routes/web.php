<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TripayCallbackController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing', [
        'products' => Product::latest()
            ->take(8)
            ->get(['id', 'sku', 'name', 'price']),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Checkout & Pembayaran TriPay
|--------------------------------------------------------------------------
*/
Route::get('checkout/selesai', [CheckoutController::class, 'finish'])->name('checkout.finish');
Route::get('checkout/{product}', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Callback dari server TriPay (tanpa CSRF, dikecualikan di bootstrap/app.php)
Route::post('tripay/callback', [TripayCallbackController::class, 'handle'])->name('tripay.callback');

/*
|--------------------------------------------------------------------------
| Admin Routes (khusus role admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('produk', ProductController::class)
            ->parameters(['produk' => 'product'])
            ->names('products');

        Route::get('pesanan', [InvoiceController::class, 'index'])->name('orders');
        Route::get('pesanan/{invoice}', [InvoiceController::class, 'show'])->name('orders.show');

        Route::get('pelanggan', fn () => Inertia::render('admin/Customers'))->name('customers');
    });

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
