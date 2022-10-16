<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';

    /** Define your route model bindings, pattern filters, etc. */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('v1')->group(function () {
                Route::middleware('web')->group(base_path('routes/web.php'));

                Route::group(['prefix' => 'panel', 'middleware' => 'web'], base_path('routes/panel.php'));

                Route::group(['prefix' => 'mobile', 'middleware' => 'api'], base_path('routes/mobile.php'));
            });
        });
    }

    /** Configure the rate limiters for the application. */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
