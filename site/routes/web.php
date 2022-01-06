<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Catalog\CheckoutController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Pharmacy\PharmacyController;
use App\Http\Controllers\Cabinet\IndexController as CabinetController;
use App\Http\Controllers\Cabinet\OrderController;
use App\Http\Controllers\Catalog\IndexController as CatalogController;
use App\Http\Controllers\Catalog\CartController;
use App\Http\Controllers\Catalog\FavoriteController;
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

//Route::get('/test', [IndexController::class, 'test']);

Route::get('/', [IndexController::class, 'index'])->name('home');
Route::get('/set-city/{city}', [IndexController::class, 'setCity'])->name('setCity');
Route::get('/alphabet/{abc}', [IndexController::class, 'alphabet'])->name('alphabet');

/* Pages */
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/advantage', [PageController::class, 'advantage'])->name('advantage');
Route::get('/delivery-booking', [PageController::class, 'deliveryBooking'])->name('deliveryBooking');
Route::get('/order-payment', [PageController::class, 'orderPayment'])->name('orderPayment');
Route::get('/processing-personal-data', [PageController::class, 'processingPersonalData'])->name('processingPersonalData');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacyPolicy');
Route::get('/rent', [PageController::class, 'rent'])->name('rent');
Route::get('/return', [PageController::class, 'return'])->name('return');
Route::get('/rules-remotely', [PageController::class, 'rulesRemotely'])->name('rulesRemotely');

Route::group(['prefix' => 'auth'], function () {
    Route::get('/login', fn () => redirect()->route('home'));
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');
});

Route::group(['prefix' => 'catalog', 'as' => 'catalog'], function () {
    Route::get('/sale', [CatalogController::class, 'sale'])->name('.sale');
    Route::get('/search', [CatalogController::class, 'search'])->name('.search');
    Route::get('/product/{product}', [CatalogController::class, 'product'])->name('.product');
    Route::get('/get-price', [CatalogController::class, 'getPrice']);
    Route::get('/{category?}', [CatalogController::class, 'index']);
});

Route::group(['prefix' => 'pharmacy', 'as' => 'pharmacy'], function () {
    Route::get('/', [PharmacyController::class, 'index']);
    Route::get('/{store}', [PharmacyController::class, 'show'])->name('.show');
});

Route::group(['prefix' => 'cart', 'as' => 'cart'], function () {
    Route::get('/', [CartController::class, 'index']);
    Route::get('/pharmacy', [CartController::class, 'pharmacy'])->name('.pharmacy');
    Route::post('/{id}', [CartController::class, 'add'])->name('.add');
    Route::put('/{id}', [CartController::class, 'change'])->name('.change');
    Route::delete('/{id}', [CartController::class, 'remove'])->name('.delete');
});

Route::group(['prefix' => 'favorite', 'as' => 'favorite'], function () {
    Route::get('/', [FavoriteController::class, 'index']);
    Route::post('/{id}', [FavoriteController::class, 'add'])->name('.add');
    Route::delete('/{id}', [FavoriteController::class, 'remove'])->name('.delete');
});

Route::group(['prefix' => 'checkout', 'as' => 'checkout'], function () {
    Route::get('/{store}', [CheckoutController::class, 'index']);
    Route::get('/finish/{order}', [CheckoutController::class, 'finish'])->name('.finish');
    Route::post('/{store}', [CheckoutController::class, 'checkout']);
});

Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'profile', 'as' => 'profile'], function () {
        Route::get('/', [CabinetController::class, 'index']);
        Route::get('/edit', [CabinetController::class, 'edit'])->name('.edit');
        Route::put('/edit', [CabinetController::class, 'update'])->name('.update');

        Route::group(['prefix' => 'order', 'as' => '.order'], function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::get('/{order}', [OrderController::class, 'show'])->name('.show');
        });
    });
});
