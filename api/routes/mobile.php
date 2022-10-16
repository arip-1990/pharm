<?php

use App\Http\Controllers\V1;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', []);
    Route::post('login', []);
    Route::post('logout', []);
});

Route::prefix('acquiring')->group(function () {
    Route::post('/', [V1\Mobile\Acquiring\IndexController::class, 'handle']);
    Route::post('/status', [V1\Mobile\Acquiring\StatusController::class, 'handle']);
});

Route::post('/checkout', [V1\Mobile\CheckoutController::class, 'handle']);
Route::post('/deliveries', [V1\Mobile\DeliveryController::class, 'handle']);
Route::post('/payments', [V1\Mobile\PaymentController::class, 'handle']);
