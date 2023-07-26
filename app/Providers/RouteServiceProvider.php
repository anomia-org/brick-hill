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
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Namespace for admin controllers
     *
     * @var string
     */
    protected $adminNamespace = 'App\Http\Controllers\Admin';

    /**
     * Namespace for superadmin controllers
     *
     * @var string
     */
    protected $superAdminNamespace = 'App\Http\Controllers\Admin\Super';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        Route::pattern('id', '-?[0-9]+');
        Route::pattern('page', '[0-9]+');
        Route::pattern('search', '.*');

        Route::model('set', \App\Models\Set\Set::class);
        Route::model('user', \App\Models\User\User::class);
        Route::model('trade', \App\Models\User\Trade::class);
        Route::model('item', \App\Models\Item\Item::class);

        parent::boot();
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('emails', function (Request $request) {
            return Limit::perMinute(1)->by($request->get('email'));
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapAdminRoutes();
    }

    /**
     * Define the admin routes for the application.
     *
     * These routes all pass through admin middleware
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        Route::middleware(['web', 'admin'])
            ->namespace($this->adminNamespace)
            ->group(base_path('routes/admin.php'));

        Route::middleware(['web', 'admin', 'role:super-admin'])
            ->namespace($this->superAdminNamespace)
            ->domain(config('site.admin_url'))
            ->group(base_path('routes/superadmin.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));

        Route::middleware(['api-passport', 'auth:api'])
            ->namespace($this->namespace)
            ->group(base_path('routes/passport.php'));
    }
}
