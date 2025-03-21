<?php

use App\Http\Controllers\{V1, V2};
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

//Route::get('test', function (\App\Product\UseCase\SearchService $service) {
//     \Illuminate\Support\Facades\DB::listen(function (\Illuminate\Database\Events\QueryExecuted $query) {
//         dump($query->sql);
//         dump($query->bindings);
//         dump($query->time);
//     });
//});

//Route::get('/get-apk-link', fn() => new JsonResponse(Storage::url('files/120на80.apk')));

Route::prefix('v1')->group(function() {

    Route::group(['prefix' => 'kids'], function() {
        Route::prefix('photo')->group(function() {
            Route::prefix('user')->group(function () {
                Route::get('/', [App\Http\Controllers\V1\PhotoKids\UserPhotoController::class, 'index']);
                Route::post('/', [App\Http\Controllers\V1\PhotoKids\UserPhotoController::class, 'store']);
            });

            Route::prefix('likes')->group(function (){
                Route::post('/{photo}', [App\Http\Controllers\V1\PhotoKids\AddLikeController::class, 'index']);
                Route::get('/myLike', [App\Http\Controllers\V1\PhotoKids\UserLikePhotoController::class, 'index']);
            });

            Route::post('/add', [App\Http\Controllers\V1\PhotoKids\AddPhotoController::class, 'index']);
            Route::get('/{age}', [App\Http\Controllers\V1\PhotoKids\GetPhotoController::class, 'index']);
        });
    });


    Route::prefix('city')->group(function () {
        Route::get('/', [V1\City\IndexController::class, 'handle']);
        Route::post('/', [V1\City\StoreController::class, 'handle']);
    });

    Route::prefix('catalog')->group(function () {
        Route::get('/stock', [V1\Catalog\StockController::class, 'handle']);
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

        Route::middleware('auth')->post('/logout', [V1\Auth\LogoutController::class, 'handle']);
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
});

Route::prefix('v2')->group(function () {
    Route::prefix('products')->group(function () {
        Route::get('/{category?}', V2\Product\IndexController::class)->whereNumber('category');
        Route::get('/search', V2\Product\SearchController::class);
        Route::get('/populars', V2\Product\PopularsController::class);
        Route::get('/discounts', V2\Product\DiscountsController::class);

        Route::prefix('{product}')->group(function () {
            Route::get('/', V2\Product\ShowController::class);
            Route::get('/price', V2\Product\ShowPriceController::class);
        })->whereUuid('product');
    });

    Route::prefix('settings')->group(function () {
        Route::get('/banners', V2\Setting\Banner\IndexController::class);
    });
});
