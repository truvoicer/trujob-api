<?php

use App\Http\Controllers\Api\Order\Discount\BulkOrderDiscountController;
use App\Http\Controllers\Api\Order\Discount\OrderDiscountController;
use App\Http\Controllers\Api\Order\Item\OrderItemController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Order\OrderSummaryController;
use App\Http\Controllers\Api\Order\Shipping\Method\OrderShippingMethodController;
use Illuminate\Support\Facades\Route;

Route::prefix('order')->name('order.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::post('/store', [OrderController::class, 'store'])->name('store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::prefix('{order}')->group(function () {
        Route::get('/summary', [OrderSummaryController::class, 'show'])->name('summary.show');
        Route::patch('/update', [OrderController::class, 'update'])->name('update');
        Route::delete('/destroy', [OrderController::class, 'destroy'])->name('destroy');

        include __DIR__ . '/order-transaction.php';

        Route::prefix('shipping')->name('shipping.')->group(function () {
            Route::prefix('method')->name('method.')->group(function () {
                Route::get('/', [OrderShippingMethodController::class, 'index'])->name('index');
                Route::prefix('{shippingMethod}')->group(function () {
                    Route::get('/', [OrderShippingMethodController::class, 'show'])->name('show');
                });
            });
        });

        Route::prefix('item')->name('item.')->group(function () {
            Route::get('/', [OrderItemController::class, 'index'])->name('index');
            Route::post('/store', [OrderItemController::class, 'store'])->name('store');
            Route::prefix('{orderItem}')->group(function () {
                Route::get('/', [OrderItemController::class, 'show'])->name('show');
                Route::patch('/update', [OrderItemController::class, 'update'])->name('update');
                Route::delete('/delete', [OrderItemController::class, 'destroy'])->name('delete');
            });
        });
        Route::prefix('discount')->name('discount.')->group(function () {
            Route::get('/', [OrderDiscountController::class, 'index'])->name('index');
            Route::prefix('bulk')->name('bulk.')->group(function () {
                Route::post('/store', BulkOrderDiscountController::class)->name('store');
            });
            Route::prefix('{discount}')->group(function () {
                Route::post('/store', [OrderDiscountController::class, 'store'])->name('store');
                Route::delete('/destroy', [OrderDiscountController::class, 'destroy'])->name('destroy');
            });
        });
    });
});
