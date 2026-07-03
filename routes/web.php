<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\AnalyticsController;

Route::get('/', [PaymentController::class, 'index']);

Route::post('/pay', [PaymentController::class, 'pay'])
    ->name('payment.pay');

Route::get('/success', [PaymentController::class, 'success'])
    ->name('payment.success');

Route::delete('/payment/{id}', [PaymentController::class, 'destroy'])
    ->name('payment.delete');

Route::get('/retry/{id}', [PaymentController::class, 'retry'])
    ->name('payment.retry');

Route::post('/retry/{id}', [PaymentController::class, 'retryPay'])
    ->name('payment.retryPay');

Route::post('/payment/{id}/refund', [RefundController::class, 'refund'])
    ->name('payment.refund');

Route::post('/webhook/mollie', [WebhookController::class, 'handle'])
    ->name('payment.webhook');

Route::get('/analytics', [AnalyticsController::class, 'index'])
    ->name('analytics.index');