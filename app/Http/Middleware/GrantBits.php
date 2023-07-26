<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class GrantBits
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
            $needsDailyCurrency = Carbon::now()->diffInHours(auth()->user()->daily_bits) >= 24;

            if($needsDailyCurrency) {
                Auth::user()->daily_bits = Carbon::now();
                Auth::user()->increment('bits', 10);
                Auth::user()->save();
            }
        }
        return $next($request);
    }
}
