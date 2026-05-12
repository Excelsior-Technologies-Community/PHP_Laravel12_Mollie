<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', [PaymentController::class, 'index']);

Route::post('/pay', [PaymentController::class, 'pay'])
    ->name('payment.pay');

Route::get('/success', [PaymentController::class, 'success'])
    ->name('payment.success');

Route::delete('/payment/{id}', [PaymentController::class, 'destroy'])
    ->name('payment.delete');