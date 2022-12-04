<?php

use App\Http\Controllers\V1\Mobile;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/', [Mobile\Auth\AuthController::class, 'handle']);
    Route::post('verify', [Mobile\Auth\VerifyController::class, 'handle']);
});

Route::prefix('user')->group(function () {
    Route::post('/', [Mobile\User\IndexController::class, 'handle']);
    Route::patch('/', [Mobile\User\UpdateController::class, 'handle']);
});

Route::prefix('acquiring')->group(function () {
    Route::post('/', [Mobile\Acquiring\IndexController::class, 'handle']);
    Route::post('/status', [Mobile\Acquiring\StatusController::class, 'handle']);
});

Route::post('/calc-order', [Mobile\CalcOrderController::class, 'handle']);
Route::post('/checkout', [Mobile\CheckoutController::class, 'handle']);
Route::post('/deliveries', [Mobile\DeliveryController::class, 'handle']);
Route::post('/payments', [Mobile\PaymentController::class, 'handle']);
