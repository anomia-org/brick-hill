<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Session;

class SessionServiceProvider extends ServiceProvider
{
    public $bindings = [
        \Illuminate\Session\Middleware\StartSession::class => \App\Extensions\Session\StartSessionTTL::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Session::extend('cachettl', function ($app) {
            $container = $app->make(\Illuminate\Contracts\Container\Container::class);
            $store = config('session.store') ?: 'redis';
            $cache = clone $container->make('cache')->store($store);
            $cache->setConnection(config('session.connection'));

            $handler = new \App\Extensions\Session\CacheBasedSessionHandler(
                $cache,
                config('session.lifetime')
            );

            return $handler;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
