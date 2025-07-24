<?php

use App\Http\Controllers\Api\Order\Transaction\PaymentGateway\PayPal\PayPalOrderTransactionCaptureController;
use App\Http\Controllers\Api\Order\Transaction\PaymentGateway\PayPal\PayPalOrderTransactionController;
use App\Http\Controllers\Api\Order\Transaction\OrderTransactionController;
use App\Http\Controllers\Api\Order\Transaction\PaymentGateway\PayPal\PayPalOrderTransactionApproveController;
use App\Http\Controllers\Api\Order\Transaction\PaymentGateway\PayPal\PayPalOrderTransactionCancelController;
use App\Http\Controllers\Api\Order\Transaction\PaymentGateway\Stripe\StripeOrderCheckoutSessionApproveController;
use App\Http\Controllers\Api\Order\Transaction\PaymentGateway\Stripe\StripeOrderCheckoutSessionCancelController;
use App\Http\Controllers\Api\Order\Transaction\PaymentGateway\Stripe\StripeOrderCheckoutSessionController;
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
        Route::delete('/delete', [OrderTransactionController::class, 'destroy'])->name('destroy');
        Route::prefix('payment-gateway')->name('payment-gateway.')->group(function () {
            Route::prefix('paypal')->name('paypal.')->group(function () {
                Route::post('/store', [PayPalOrderTransactionController::class, 'store'])->name('store');
                Route::post('/capture/store', [PayPalOrderTransactionCaptureController::class, 'store'])->name('capture.store');
                Route::post('/approve/store', [PayPalOrderTransactionApproveController::class, 'store'])->name('approve.store');
                Route::post('/cancel/store', [PayPalOrderTransactionCancelController::class, 'store'])->name('cancel.store');
            });
            Route::prefix('stripe')->name('stripe.')->group(function () {
                Route::prefix('checkout-session')->name('checkout-session.')->group(function () {
                    Route::post('/store', [StripeOrderCheckoutSessionController::class, 'store'])->name('store');
                    Route::post('/approve/store', [StripeOrderCheckoutSessionApproveController::class, 'store'])->name('approve.store');
                    Route::post('/cancel/store', [StripeOrderCheckoutSessionCancelController::class, 'store'])->name('cancel.store');
                });
            });
        });
    });
});
