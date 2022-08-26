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

Route::post('/pay', [Api\PayController::class, 'handle']);

Route::prefix('v1')->group(function () {
    Route::post('/login', [Api\V1\Auth\LoginController::class, 'handle']);
    Route::prefix('deliveries')->group(function () {
        Route::POST('/', [Api\V1\Delivery\IndexController::class, 'handle']);
    });
    Route::prefix('payments')->group(function () {
        Route::POST('/', [Api\V1\Payment\IndexController::class, 'handle']);
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth', [Api\V1\Auth\IndexController::class, 'handle']);
        Route::post('/logout', [Api\V1\Auth\LogoutController::class, 'handle']);

        Route::prefix('user')->group(function () {
            Route::get('/', [Api\V1\User\IndexController::class, 'handle']);
            Route::get('/{user}', [Api\V1\User\ShowController::class, 'handle']);
        });

        Route::prefix('product')->group(function () {
            Route::prefix('/upload')->group(function () {
                Route::patch('/', [Api\V1\Product\UpdatePhotoController::class, 'handle']);
                Route::delete('/', [Api\V1\Product\DeletePhotoController::class, 'handle']);
                Route::patch('/status', [Api\V1\Product\UpdateStatusPhotoController::class, 'handle']);
                Route::post('/{product}', [Api\V1\Product\UploadController::class, 'handle']);
            });

            Route::prefix('/moderation')->group(function () {
                Route::get('/', [Api\V1\Product\Moderation\IndexController::class, 'handle']);
                Route::put('/{product}', [Api\V1\Product\Moderation\UpdateController::class, 'handle']);
            });

            Route::get('/', [Api\V1\Product\IndexController::class, 'handle']);
            Route::get('/{product}', [Api\V1\Product\ShowController::class, 'handle']);
            Route::put('/attributes/{product}', [Api\V1\Product\UpdateAttributesController::class, 'handle']);
            Route::put('/description/{product}', [Api\V1\Product\UpdateDescriptionController::class, 'handle']);
            Route::put('/{product}', [Api\V1\Product\UpdateController::class, 'handle']);
        });

        Route::prefix('order')->group(function () {
            Route::get('/', [Api\V1\Order\IndexController::class, 'handle']);
            Route::get('/{order}', [Api\V1\Order\ShowController::class, 'handle']);
            Route::get('/{order}/items', [Api\V1\Order\ShowItemsController::class, 'handle']);
            Route::get('/{order}/send-data', [Api\V1\Order\SendDataController::class, 'handle']);
        });

        Route::prefix('offer')->group(function () {
            Route::get('/', [Api\V1\Offer\IndexController::class, 'handle']);
            Route::get('/{product}', [Api\V1\Offer\ShowController::class, 'handle']);
        });

        Route::get('/category', [Api\V1\Category\IndexController::class, 'handle']);
        Route::get('/statistic', [Api\V1\Statistic\IndexController::class, 'handle']);
        Route::get('/attribute', [Api\V1\Attribute\IndexController::class, 'handle']);
    });
});
