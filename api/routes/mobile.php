<?php

use App\Http\Controllers\V1\Mobile;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [Mobile\Auth\RegisterController::class, 'handle']);
    Route::post('login', [Mobile\Auth\LoginController::class, 'handle']);
    Route::post('verify-phone', [Mobile\Auth\VerifyPhoneController::class, 'handle']);
//    Route::post('logout', []);
});

Route::prefix('acquiring')->group(function () {
    Route::post('/', [Mobile\Acquiring\IndexController::class, 'handle']);
    Route::post('/status', [Mobile\Acquiring\StatusController::class, 'handle']);
});

Route::post('/checkout', [Mobile\CheckoutController::class, 'handle']);
Route::post('/deliveries', [Mobile\DeliveryController::class, 'handle']);
Route::post('/payments', [Mobile\PaymentController::class, 'handle']);
