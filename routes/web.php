<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('stripe/webhook', 'Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook')->name('cashier.webhook');
