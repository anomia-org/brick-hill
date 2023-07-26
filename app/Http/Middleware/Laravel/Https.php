<?php

namespace App\Http\Middleware\Laravel;

use Closure;

class Https
{
    protected $except = [
        '/API/client/*',
        '/API/games/*',
        '/download/*',
        '/v1/auth/verifyToken',
        '/v1/games/retrieveAsset',
        '/v1/alb/healthcheck'
    ];

    public function handle($request, Closure $next)
    {
        if (!$request->secure() && !$this->inExceptArray($request) && (app()->environment() != 'local' && app()->environment() != 'testing')) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }

    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
