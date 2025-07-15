<?php

use App\Http\Controllers\Api\Order\Transaction\PaymentGateway\PayPal\PayPalOrderTransactionCaptureController;
use App\Http\Controllers\Api\Order\Transaction\PaymentGateway\PayPal\PayPalOrderTransactionController;
use App\Http\Controllers\Api\Order\Transaction\OrderTransactionController;
use App\Http\Controllers\Api\PaymentGateway\PayPal\PayPalOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('transaction')->name('transaction.')->group(function () {
    Route::get('/', [OrderTransactionController::class, 'index'])->name('index');
    Route::post('/store', [OrderTransactionController::class, 'store'])->name('store');
    Route::prefix('payment-gateway')->name('payment-gateway.')->group(function () {
        Route::prefix('paypal')->name('paypal.')->group(function () {
            Route::get('/', [PayPalOrderController::class, 'index'])->name('index');
        });
    });
    Route::prefix('{transaction}')->group(function () {
        Route::get('/', [OrderTransactionController::class, 'show'])->name('show');
        Route::patch('/update', [OrderTransactionController::class, 'update'])->name('update');
        Route::delete('/delete', [OrderTransactionController::class, 'destroy'])->name('delete');
        Route::prefix('payment-gateway')->name('payment-gateway.')->group(function () {
            Route::prefix('paypal')->name('paypal.')->group(function () {
                Route::post('/store', [PayPalOrderTransactionController::class, 'store'])->name('store');
                Route::post('/capture/store', [PayPalOrderTransactionCaptureController::class, 'store'])->name('show');
            });
        });
    });
});
