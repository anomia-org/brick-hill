<?php

namespace App\Http\Middleware\Laravel;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Cookie;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        'stripe/webhook',
        'paypal/webhook',
        'v1/alb/healthcheck',
        'v1/assets/*'
    ];

    /**
     * Redirect the user back to the root of the application with a maintenance mode bypass cookie.
     *
     * @param  string  $secret
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function bypassResponse(string $secret)
    {
        return redirect('/')->withCookie(
            $this->createCookie($secret)
        );
    }

    /**
     * Create a new maintenance mode bypass cookie.
     *
     * @param  string  $key
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public static function createCookie(string $key)
    {
        $expiresAt = Carbon::now()->addHours(12);

        return new Cookie('laravel_maintenance', base64_encode(json_encode([
            'expires_at' => $expiresAt->getTimestamp(),
            'mac' => hash_hmac('sha256', (string) $expiresAt->getTimestamp(), $key),
        ])), $expiresAt, config('session.path'), config('session.domain'));
    }
}
