<?php

use App\Http\Controllers\V1;
use Illuminate\Support\Facades\Route;

Route::post('/pay', [V1\PayController::class, 'handle']);

Route::group(['prefix' => '1c', 'middleware' => 'auth.basic.once'], function () {
    Route::get('/category', [V1\Category\IndexController::class, 'handle']);
    Route::post('/feed', [V1\FeedController::class, 'handle']);
    Route::post('/order', [V1\Order\UpdateController::class, 'handle']);
});
