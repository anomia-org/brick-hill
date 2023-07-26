<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class PassInfoToNginx
{
    /**
     * Add headers to request for Nginx logging
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // route isnt determined until after response
        $response = $next($request);

        if ($request->route())
            $response->headers->set(
                'X-Route',
                str_replace(
                    '\\',
                    '/',
                    str_replace('App\Http\Controllers\\', '', Route::currentRouteAction())
                )
            );

        if (Auth::check())
            $response->headers->set(
                'X-Log-Metadata',
                'LoggedIn-' . Auth::user()->id
            );

        return $response;
    }
}
