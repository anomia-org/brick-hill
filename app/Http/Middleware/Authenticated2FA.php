<?php

namespace App\Http\Middleware;

use Closure;
use Google2FA;
use Illuminate\Support\Facades\{
    Auth,
    Route
};

use App\Support\TFAAuthenticator;

class Authenticated2FA
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
        /** @var TFAAuthenticator */
        $authenticator = app(TFAAuthenticator::class)->boot($request);
        $authed = $authenticator->isAuthenticated();

        if (Auth::check()) {
            // if logged in go ahead
            if ($authed || Route::currentRouteName() == 'logout')
                return $next($request);

            if ($request->subdomain() == 'api' || $request->subdomain() == 'laravel')
                return redirect('https://brick-hill.com');

            return $authenticator->makeRequestOneTimePasswordResponse();
        } else {
            // if not logged in but still has google2fa login logout of 2fa
            if ($authed)
                Google2FA::logout();
        }

        return $next($request);
    }
}
