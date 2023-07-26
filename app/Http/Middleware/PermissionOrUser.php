<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Spatie\Permission\Middlewares\PermissionMiddleware;

class PermissionOrUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @param  string  $model
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission, $model)
    {
        if($this->getModel($request, $model)->is(auth()->user()))
            return $next($request);
            
        return app(PermissionMiddleware::class)->handle($request, function ($request) use ($next) {
            return $next($request);
        }, $permission);
    }

    /**
     * Get the model to authorize.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $model
     * @return \Illuminate\Database\Eloquent\Model|string
     */
    protected function getModel($request, $model)
    {
        if ($this->isClassName($model)) {
            return trim($model);
        } else {
            return $request->route($model, null) ?:
                ((preg_match("/^['\"](.*)['\"]$/", trim($model), $matches)) ? $matches[1] : null);
        }
    }

    /**
     * Checks if the given string looks like a fully qualified class name.
     *
     * @param  string  $value
     * @return bool
     */
    protected function isClassName($value)
    {
        return strpos($value, '\\') !== false;
    }
}
