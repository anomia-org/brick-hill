<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class UpdateLastOnline
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
        if(Auth::check()) {
            if(Carbon::parse(Auth::user()->last_online)->addMinute()->isPast()) {
                Auth::user()->last_online = Carbon::now();
                Auth::user()->save();
            }
        }

        return $next($request);
    }
}
