<?php

namespace App\Http\Middleware\API;

use Closure;

class SetDefaultValues
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
        $request->request->set('limit', $request->limit ?? 10);
        $request->request->set('cursor', $request->cursor ?? 0);
        if ($request->get('limit') > 100 || $request->get('limit') < 1)
            return throw new \App\Exceptions\Custom\APIException('Limit must be less than or equal to 100');

        return $next($request);
    }
}
