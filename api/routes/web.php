<?php

use App\Http\Controllers\V1;
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
    \Illuminate\Support\Facades\Artisan::call('import:category');
});

Route::post('/pay', [V1\PayController::class, 'handle']);

Route::group(['prefix' => '1c', 'middleware' => 'auth.basic.once'], function () {
    Route::post('/feed', [V1\FeedController::class, 'handle']);
    Route::post('/order', [V1\Order\UpdateController::class, 'handle']);
});

Route::prefix('city')->group(function () {
    Route::get('/', [V1\City\IndexController::class, 'handle']);
    Route::post('/', [V1\City\StoreController::class, 'handle']);
});

Route::prefix('catalog')->group(function () {
//    Route::get('/sale', [Catalog\PopularController::class, 'handle']);
    Route::get('/search', [V1\Catalog\SearchController::class, 'handle']);
    Route::get('/popular', [V1\Catalog\PopularController::class, 'handle']);
    Route::get('/{category?}', [V1\Catalog\IndexController::class, 'handle']);

    Route::prefix('product')->group(function () {
        Route::get('/{product}', [V1\Catalog\Product\IndexController::class, 'handle']);
        Route::get('/{product}/price', [V1\Catalog\Product\PriceController::class, 'handle']);
    });
});

Route::prefix('category')->group(function () {
    Route::get('/', [V1\Category\IndexController::class, 'handle']);
});

Route::prefix('store')->group(function () {
    Route::get('/', [V1\Store\IndexController::class, 'handle']);
    Route::get('/{store}', [V1\Store\ShowController::class, 'handle']);
});

Route::prefix('offer')->group(function () {
    Route::get('/{product}', [V1\Offer\IndexController::class, 'handle']);
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [V1\Auth\LoginController::class, 'handle']);
    Route::post('/register', [V1\Auth\RegisterController::class, 'handle']);
    Route::post('/set-password', [V1\Auth\SetPasswordController::class, 'handle']);

    Route::prefix('reset')->group(function () {
        Route::get('/password', [V1\Auth\Reset\RequestChangePasswordController::class, 'handle']);
        Route::post('/password', [V1\Auth\Reset\ChangePasswordController::class, 'handle']);
        Route::post('/password/validate', [V1\Auth\Reset\ValidateTempPasswordController::class, 'handle']);
    });

    Route::prefix('verify')->group(function () {
        Route::get('/phone', [V1\Auth\Verify\RequestPhoneVerifyController::class, 'handle']);
        Route::post('/phone', [V1\Auth\Verify\VerifyPhoneController::class, 'handle']);
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [V1\Auth\LogoutController::class, 'handle']);
        Route::post('/refresh', [V1\Auth\RefreshController::class, 'handle']);
    });
});

Route::middleware('auth')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [V1\User\IndexController::class, 'handle']);
        Route::patch('/', [V1\User\UpdateController::class, 'handle']);
        Route::put('/update', [V1\User\UpdateController::class, 'handle']);
        Route::put('/update-password', [V1\User\UpdatePasswordController::class, 'handle']);
    });

    Route::prefix('card')->group(function () {
        Route::get('/', [V1\Card\IndexController::class, 'handle']);
        Route::put('/block/{cardId}', [V1\Card\BlockController::class, 'handle']);
    });

    Route::prefix('cheque')->group(function () {
        Route::get('/', [V1\Cheque\IndexController::class, 'handle']);
    });

    Route::prefix('bonus')->group(function () {
        Route::get('/', [V1\Bonus\IndexController::class, 'handle']);
    });

    Route::prefix('coupon')->group(function () {
        Route::get('/', [V1\Coupon\IndexController::class, 'handle']);
    });

    Route::prefix('order')->group(function () {
        Route::get('/', [V1\Order\IndexController::class, 'index']);

        Route::prefix('checkout')->group(function () {
            Route::post('/', [V1\Order\Checkout\IndexController::class, 'handle']);
            Route::post('/store', [V1\Order\Checkout\StoreController::class, 'handle']);
        });
    });
});

