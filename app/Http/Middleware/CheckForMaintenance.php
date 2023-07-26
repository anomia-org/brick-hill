<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\{
    Redis,
    Artisan
};

class CheckForMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $check = Redis::get("maintenance");
        if($check && !app()->isDownForMaintenance()) 
            Artisan::call("down --secret=".config('site.maintenance.key'));            
        if(!$check && app()->isDownForMaintenance())
            Artisan::call("up");

        return $next($request);
    }
}
