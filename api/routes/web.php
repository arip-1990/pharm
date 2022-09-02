<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\Bonus;
use App\Http\Controllers\Card;
use App\Http\Controllers\Catalog;
use App\Http\Controllers\Category;
use App\Http\Controllers\Cheque;
use App\Http\Controllers\CityController;
use App\Http\Controllers\Coupon;
use App\Http\Controllers\Delivery;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\Offer;
use App\Http\Controllers\Order;
use App\Http\Controllers\Panel;
use App\Http\Controllers\PayController;
use App\Http\Controllers\Payment;
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

//Route::get('/test', function () {});

Route::group(['prefix' => '1c', 'middleware' => 'auth.basic.once'], function () {
    Route::post('/feed', [FeedController::class, 'handle']);
    Route::post('/order', [Order\UpdateController::class, 'handle']);
});

Route::prefix('order')->group(function () {
    Route::post('/', [Order\CheckoutController::class, 'handle']);
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
//    Route::get('/sale', [Catalog\PopularController::class, 'handle']);
    Route::get('/search', [Catalog\SearchController::class, 'handle']);
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

Route::prefix('checkout')->group(function () {
    Route::get('/', [Order\Checkout\IndexController::class, 'handle']);
    Route::get('/store', [Order\Checkout\StoreController::class, 'handle']);
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [Auth\LoginController::class, 'handle']);
    Route::post('/register', [Auth\RegisterController::class, 'handle']);
    Route::post('/set-password', [Auth\SetPasswordController::class, 'handle']);

    Route::prefix('verify')->group(function () {
        Route::get('/phone', [Auth\Verify\RequestPhoneVerifyController::class, 'handle']);
        Route::post('/phone', [Auth\Verify\VerifyPhoneController::class, 'handle']);
    });

    Route::post('/logout', [Auth\LogoutController::class, 'handle'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [User\IndexController::class, 'handle']);
        Route::patch('/', [User\UpdateController::class, 'handle']);
        Route::put('/update', [User\UpdateController::class, 'handle']);
        Route::put('/update-password', [User\UpdatePasswordController::class, 'handle']);
    });

    Route::prefix('deliveries')->group(function () {
        Route::post('/', [Delivery\IndexController::class, 'handle']);
    });

    Route::prefix('payments')->group(function () {
        Route::post('/', [Payment\IndexController::class, 'handle']);
    });

    Route::prefix('card')->group(function () {
        Route::get('/', [Card\IndexController::class, 'handle']);
        Route::put('/block/{cardId}', [Card\BlockController::class, 'handle']);
    });

    Route::prefix('cheque')->group(function () {
        Route::get('/', [Cheque\IndexController::class, 'handle']);
    });

    Route::prefix('bonus')->group(function () {
        Route::get('/', [Bonus\IndexController::class, 'handle']);
    });

    Route::prefix('coupon')->group(function () {
        Route::get('/', [Coupon\IndexController::class, 'handle']);
    });

    Route::prefix('order')->group(function () {
        Route::get('/', [Order\IndexController::class, 'index']);
    });
});

