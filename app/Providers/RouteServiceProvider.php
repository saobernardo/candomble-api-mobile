<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/**
 * Class RouteServiceProvider
 *
 * Defines API route mappings and rate limiting for the application.
 */
class RouteServiceProvider extends ServiceProvider
{
    protected string $apiNamespace = 'App\Http\Controllers';

    public const HOME = '/home';

    /**
     * Bootstrap any application services.
     *
     * Configures rate limiting and registers API routes.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->namespace($this->apiNamespace)
                ->prefix('api/user')
                ->group(base_path('routes/user.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * Currently disables API rate limiting (no limit).
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function () {
            return Limit::none();
        });
    }
}
