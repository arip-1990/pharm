<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\Catalog;
use App\Http\Controllers\Category;
use App\Http\Controllers\CityController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\Offer;
use App\Http\Controllers\Order;
use App\Http\Controllers\Panel;
use App\Http\Controllers\PayController;
use App\Http\Controllers\Store;
use App\Http\Controllers\User;
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

Route::get('/test', function () {
//    $service = new \App\UseCases\PosService();
//    $data = $service->getBalance('79289844468');
});

Route::prefix('1c')->group(function () {
    Route::get('/feed', [FeedController::class, 'handle']);
    Route::post('/order', [Order\UpdateController::class, 'handle'])->middleware('auth.basic.once');
});

Route::post('/pay', [PayController::class, 'handle']);

Route::prefix('panel')->group(function () {
    Route::post('/login', [Panel\Auth\LoginController::class, 'handle']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [Panel\Auth\LogoutController::class, 'handle']);
        Route::get('/user', [Panel\User\IndexController::class, 'handle']);

        Route::prefix('product')->group(function () {
            Route::get('/', [Panel\Product\IndexController::class, 'handle']);
            Route::get('/{product}', [Panel\Product\ShowController::class, 'handle']);
            Route::put('/attributes/{product}', [Panel\Product\UpdateAttributesController::class, 'handle']);
            Route::put('/description/{product}', [Panel\Product\UpdateDescriptionController::class, 'handle']);
            Route::put('/{product}', [Panel\Product\UpdateController::class, 'handle']);
            Route::post('/upload/{product}', [Panel\Product\UploadController::class, 'handle']);
            Route::delete('/upload/{photo}', [Panel\Product\DeletePhotoController::class, 'handle']);
        });

        Route::prefix('order')->group(function () {
            Route::get('/', [Panel\Order\IndexController::class, 'handle']);
            Route::get('/{order}', [Panel\Order\ShowController::class, 'handle']);
        });

        Route::prefix('offer')->group(function () {
            Route::get('/', [Panel\Offer\IndexController::class, 'handle']);
            Route::get('/{product}', [Panel\Offer\ShowController::class, 'handle']);
        });

        Route::get('/category', [Panel\Category\IndexController::class, 'handle']);
        Route::get('/statistic', [Panel\Statistic\IndexController::class, 'handle']);
        Route::get('/attribute', [Panel\Attribute\IndexController::class, 'handle']);
    });
});

Route::get('/city', [CityController::class, 'handle']);

Route::prefix('catalog')->group(function () {
    Route::get('/sale', [Catalog\PopularController::class, 'handle']);
    Route::get('/search', [Catalog\PopularController::class, 'handle']);
    Route::get('/popular', [Catalog\PopularController::class, 'handle']);
    Route::get('/product/{product}', [Catalog\ProductController::class, 'handle']);
    Route::get('/{category?}', [Catalog\IndexController::class, 'handle']);
});

Route::prefix('category')->group(function () {
    Route::get('/', [Category\IndexController::class, 'handle']);
});

Route::prefix('store')->group(function () {
    Route::get('/', [Store\IndexController::class, 'handle']);
    Route::get('/{store}', [Store\ShowController::class, 'handle']);
});

Route::prefix('offer')->group(function () {
    Route::get('/{product}', [Offer\IndexController::class, 'handle']);
});

Route::post('/login', [Auth\LoginController::class, 'handle']);
Route::post('/register', [Auth\RegisterController::class, 'handle']);

Route::prefix('checkout')->group(function () {
    Route::get('/', [Order\Checkout\IndexController::class, 'handle']);
    Route::get('/store', [Order\Checkout\StoreController::class, 'handle']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [User\IndexController::class, 'handle']);
    Route::post('/logout', [Auth\LogoutController::class, 'handle']);

    Route::prefix('order')->group(function () {
        Route::get('/', [Order\IndexController::class, 'index']);
    });


});

