<?php

use App\Http\Controllers\V1;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [V1\Panel\Auth\LoginController::class, 'handle']);

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [V1\Panel\Auth\LogoutController::class, 'handle']);
        Route::get('/user', [V1\Panel\Auth\UserController::class, 'handle']);
    });
});

Route::middleware('auth')->group(function () {
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
