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

Route::post('/edit-order', [Api\V1\Order\EditController::class, 'handle'])->middleware('auth.basic.once');
Route::get('/pay', [Api\PayController::class, 'handle']);

Route::prefix('v1')->group(function () {
    Route::post('/login', [Api\V1\Auth\LoginController::class, 'handle']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [Api\V1\Auth\LogoutController::class, 'handle']);
        Route::get('/user', [Api\V1\User\IndexController::class, 'handle']);

        Route::prefix('product')->group(function () {
            Route::get('/', [Api\V1\Product\IndexController::class, 'handle']);
            Route::get('/{product}', [Api\V1\Product\ShowController::class, 'handle']);
            Route::put('/attributes/{product}', [Api\V1\Product\UpdateAttributesController::class, 'handle']);
            Route::put('/description/{product}', [Api\V1\Product\UpdateDescriptionController::class, 'handle']);
            Route::put('/{product}', [Api\V1\Product\UpdateController::class, 'handle']);
            Route::post('/upload/{product}', [Api\V1\Product\UploadController::class, 'handle']);
            Route::delete('/upload/{photo}', [Api\V1\Product\DeletePhotoController::class, 'handle']);
        });

        Route::prefix('order')->group(function () {
            Route::get('/', [Api\V1\Order\IndexController::class, 'handle']);
            Route::get('/{order}', [Api\V1\Order\ShowController::class, 'handle']);
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
