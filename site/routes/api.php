<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/edit-order', [Api\Order\EditController::class, 'handle'])->middleware('auth.basic.once');

Route::post('/login', [Api\Auth\LoginController::class, 'handle']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [Api\Auth\LogoutController::class, 'handle']);
    Route::get('/user', [Api\User\IndexController::class, 'handle']);

    Route::group(['prefix' => 'product'], function () {
        Route::get('/', [Api\Product\IndexController::class, 'handle']);
        Route::get('/{product}', [Api\Product\ShowController::class, 'handle']);
        Route::put('/description/{product}', [Api\Product\UpdateDescriptionController::class, 'handle']);
        Route::put('/{product}', [Api\Product\UpdateController::class, 'handle']);
    });
    Route::get('/category', [Api\Category\IndexController::class, 'handle']);
    Route::get('/order', [Api\Order\IndexController::class, 'handle']);
    Route::get('/statistic', [Api\Statistic\IndexController::class, 'handle']);
});
