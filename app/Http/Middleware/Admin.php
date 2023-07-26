<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\{
    Auth,
    Route
};

use App\Models\User\Admin\AdminLog;

class Admin
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
        if (!Auth::check() || !Auth::user()->is_admin)
            return abort(404);

        if (!Auth::user()->tfa_active)
            return error('You must have 2FA enabled to access admin features');

        if (
            $request->isMethod('POST') &&
            (Route::currentRouteAction() != 'App\Http\Controllers\Admin\API\PermissionController@canUserAccess' &&
                Route::currentRouteAction() != 'App\Http\Controllers\Admin\API\ThumbnailController@bulkThumbnails')
        ) {
            $params = Route::current()->originalParameters();
            $reqParams = $request->all();
            $comb = array_merge($params, $reqParams);
            unset($comb['_token']);
            AdminLog::create([
                'user_id' => auth()->id(),
                'action' => str_replace('App\Http\Controllers\\', '', Route::currentRouteAction()),
                'post_data' => json_encode($comb)
            ]);
        }

        return $next($request);
    }
}
