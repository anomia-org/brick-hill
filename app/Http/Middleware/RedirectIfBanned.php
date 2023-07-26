<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\{
    Auth,
    Route
};

class RedirectIfBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $checkForBan = Auth::user()->bans()->active()->exists();

            $acceptedRoutes = [
                'banned',
                'logout',
                'postBanned',
                'guidelines',
                'tos',
                'privacy',
                'billingCancelSubscription',
                'billingPortal'
            ];

            if ($checkForBan && !in_array(Route::currentRouteName(), $acceptedRoutes))
                return redirect()->route('banned');
        }

        return $next($request);
    }
}
