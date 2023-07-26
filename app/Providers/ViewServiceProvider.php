<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Http\View\Composers\{
    Event,
    Banner,
    CheckVerifiedEmail
};

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //view()->composer('layouts.header', Event::class);
        view()->composer('layouts.header', Banner::class);
        view()->composer('layouts.header', CheckVerifiedEmail::class);
    }
}
