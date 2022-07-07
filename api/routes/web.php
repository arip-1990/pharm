<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayController;
use App\Http\Controllers\V1;
use App\Http\Controllers\V2;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('1c')->group(function () {
    Route::post('/order', [OrderController::class, 'handle'])->middleware('auth.basic.once');
});

Route::post('/pay', [PayController::class, 'handle']);

Route::prefix('v1')->group(function () {
    Route::post('/login', [V1\Auth\LoginController::class, 'handle']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [V1\Auth\LogoutController::class, 'handle']);
        Route::get('/user', [V1\User\IndexController::class, 'handle']);

        Route::prefix('product')->group(function () {
            Route::get('/', [V1\Product\IndexController::class, 'handle']);
            Route::get('/{product}', [V1\Product\ShowController::class, 'handle']);
            Route::put('/attributes/{product}', [V1\Product\UpdateAttributesController::class, 'handle']);
            Route::put('/description/{product}', [V1\Product\UpdateDescriptionController::class, 'handle']);
            Route::put('/{product}', [V1\Product\UpdateController::class, 'handle']);
            Route::post('/upload/{product}', [V1\Product\UploadController::class, 'handle']);
            Route::delete('/upload/{photo}', [V1\Product\DeletePhotoController::class, 'handle']);
        });

        Route::prefix('order')->group(function () {
            Route::get('/', [V1\Order\IndexController::class, 'handle']);
            Route::get('/{order}', [V1\Order\ShowController::class, 'handle']);
        });

        Route::prefix('offer')->group(function () {
            Route::get('/', [V1\Offer\IndexController::class, 'handle']);
            Route::get('/{product}', [V1\Offer\ShowController::class, 'handle']);
        });

        Route::get('/category', [V1\Category\IndexController::class, 'handle']);
        Route::get('/statistic', [V1\Statistic\IndexController::class, 'handle']);
        Route::get('/attribute', [V1\Attribute\IndexController::class, 'handle']);
    });
});

Route::prefix('v2')->group(function () {
    Route::get('/city', [V2\CityController::class, 'handle']);

    Route::prefix('catalog')->group(function () {
        Route::get('/sale', [V2\Product\PopularController::class, 'handle']);
        Route::get('/search', [V2\Product\PopularController::class, 'handle']);
        Route::get('/popular', [V2\Product\PopularController::class, 'handle']);
        Route::get('/product/{product}', [V2\Product\ProductController::class, 'handle']);
        Route::get('/{category?}', [V2\Product\IndexController::class, 'handle']);
    });

    Route::prefix('category')->group(function () {
        Route::get('/', [V2\Category\IndexController::class, 'handle']);
    });

    Route::prefix('store')->group(function () {
        Route::get('/', [V2\Store\IndexController::class, 'handle']);
        Route::get('/{store}', [V2\Store\ShowController::class, 'handle']);
    });

    Route::prefix('cart')->group(function () {
        Route::get('/', [V2\CartController::class, 'index']);
        Route::post('/{id}', [V2\CartController::class, 'store']);
        Route::put('/{id}', [V2\CartController::class, 'update']);
        Route::delete('/{id}', [V2\CartController::class, 'delete']);
    });

    Route::post('/login', [V2\Auth\LoginController::class, 'handle']);
    Route::post('/register', [V2\Auth\RegisterController::class, 'handle']);
    Route::post('/logout', [V2\Auth\LogoutController::class, 'handle']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [V2\User\IndexController::class, 'handle']);

        Route::prefix('order')->group(function () {
            Route::get('/', [V2\OrderController::class, 'index']);
        });
    });
});
