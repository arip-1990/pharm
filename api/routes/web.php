<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\Bonus;
use App\Http\Controllers\Card;
use App\Http\Controllers\Catalog;
use App\Http\Controllers\Category;
use App\Http\Controllers\Cheque;
use App\Http\Controllers\City;
use App\Http\Controllers\Coupon;
use App\Http\Controllers\Offer;
use App\Http\Controllers\Order;
use App\Http\Controllers\Store;
use App\Http\Controllers\User;
use App\Http\Controllers\V1;
use Carbon\Exceptions\InvalidFormatException;
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
    //
});

Route::group(['prefix' => '1c', 'middleware' => 'auth.basic.once'], function () {
    Route::post('/feed', [V1\FeedController::class, 'handle']);
    Route::post('/order', [Order\UpdateController::class, 'handle']);
});

// TODO delete routes
Route::post('/deliveries', [V1\Mobile\DeliveryController::class, 'handle']);
Route::post('/payments', [V1\Mobile\PaymentController::class, 'handle']);
//

Route::prefix('v1')->group(function () {
    Route::post('/pay', [V1\PayController::class, 'handle']);

    Route::prefix('mobile')->group(function () {
        Route::post('/checkout', [V1\Mobile\CheckoutController::class, 'handle']);
        Route::post('/deliveries', [V1\Mobile\DeliveryController::class, 'handle']);
        Route::post('/payments', [V1\Mobile\PaymentController::class, 'handle']);
    });

    Route::prefix('auth')->group(function () {
        Route::post('/login', [V1\Panel\Auth\LoginController::class, 'handle']);

        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [V1\Panel\Auth\LogoutController::class, 'handle']);
            Route::get('/user', [V1\Panel\Auth\UserController::class, 'handle']);
        });
    });

    Route::group(['prefix' => 'panel', 'middleware' => 'auth:api'], function () {
        Route::prefix('user')->group(function () {
            Route::get('/', [V1\Panel\User\IndexController::class, 'handle']);
            Route::get('/{user}', [V1\Panel\User\ShowController::class, 'handle']);
            Route::patch('/', [V1\Panel\User\UpdateController::class, 'handle']);
        });

        Route::prefix('product')->group(function () {
            Route::prefix('/upload')->group(function () {
                Route::patch('/', [V1\Panel\Product\UpdatePhotoController::class, 'handle']);
                Route::delete('/', [V1\Panel\Product\DeletePhotoController::class, 'handle']);
                Route::patch('/status', [V1\Panel\Product\UpdateStatusPhotoController::class, 'handle']);
                Route::post('/{product}', [V1\Panel\Product\UploadController::class, 'handle']);
            });

            Route::prefix('/moderation')->group(function () {
                Route::get('/', [V1\Panel\Product\Moderation\IndexController::class, 'handle']);
                Route::put('/{product}', [V1\Panel\Product\Moderation\UpdateController::class, 'handle']);
            });

            Route::get('/', [V1\Panel\Product\IndexController::class, 'handle']);
            Route::get('/{product}', [V1\Panel\Product\ShowController::class, 'handle']);
            Route::put('/attributes/{product}', [V1\Panel\Product\UpdateAttributesController::class, 'handle']);
            Route::put('/description/{product}', [V1\Panel\Product\UpdateDescriptionController::class, 'handle']);
            Route::put('/{product}', [V1\Panel\Product\UpdateController::class, 'handle']);
        });

        Route::prefix('order')->group(function () {
            Route::get('/', [V1\Panel\Order\IndexController::class, 'handle']);
            Route::get('/{order}', [V1\Panel\Order\ShowController::class, 'handle']);
            Route::get('/{order}/items', [V1\Panel\Order\ShowItemsController::class, 'handle']);
            Route::get('/{order}/send-data', [V1\Panel\Order\SendDataController::class, 'handle']);
        });

        Route::prefix('offer')->group(function () {
            Route::get('/', [V1\Panel\Offer\IndexController::class, 'handle']);
            Route::get('/{product}', [V1\Panel\Offer\ShowController::class, 'handle']);
        });

        Route::get('/category', [V1\Panel\Category\IndexController::class, 'handle']);
        Route::get('/statistic', [V1\Panel\Statistic\IndexController::class, 'handle']);
        Route::get('/attribute', [V1\Panel\Attribute\IndexController::class, 'handle']);
    });
});

Route::prefix('city')->group(function () {
    Route::get('/', [City\IndexController::class, 'handle']);
    Route::post('/', [City\StoreController::class, 'handle']);
});

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

Route::prefix('auth')->group(function () {
    Route::post('/login', [Auth\LoginController::class, 'handle']);
    Route::post('/register', [Auth\RegisterController::class, 'handle']);
    Route::post('/set-password', [Auth\SetPasswordController::class, 'handle']);

    Route::prefix('reset')->group(function () {
        Route::get('/password', [Auth\Reset\RequestChangePasswordController::class, 'handle']);
        Route::post('/password', [Auth\Reset\ChangePasswordController::class, 'handle']);
        Route::post('/password/validate', [Auth\Reset\ValidateTempPasswordController::class, 'handle']);
    });

    Route::prefix('verify')->group(function () {
        Route::get('/phone', [Auth\Verify\RequestPhoneVerifyController::class, 'handle']);
        Route::post('/phone', [Auth\Verify\VerifyPhoneController::class, 'handle']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [Auth\LogoutController::class, 'handle']);
        Route::post('/refresh', [Auth\RefreshController::class, 'handle']);
    });
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [User\IndexController::class, 'handle']);
        Route::patch('/', [User\UpdateController::class, 'handle']);
        Route::put('/update', [User\UpdateController::class, 'handle']);
        Route::put('/update-password', [User\UpdatePasswordController::class, 'handle']);
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

        Route::prefix('checkout')->group(function () {
            Route::post('/', [Order\Checkout\IndexController::class, 'handle']);
            Route::post('/store', [Order\Checkout\StoreController::class, 'handle']);
        });
    });
});

