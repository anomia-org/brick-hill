<?php

namespace App\Extensions\Session;

use Illuminate\Session\Middleware\StartSession as StartSession;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{
    Auth,
    Date
};

class StartSessionTTL extends StartSession
{
    /**
     * Get the session lifetime in seconds.
     * This only appears to be used for garbage collection, not sure if it should be changed to shorter TTL
     *
     * @return int
     */
    protected function getSessionLifetimeInSeconds()
    {
        return ($this->manager->getSessionConfig()['lifetime'] ?? null) * 60;
    }

    /**
     * Get the cookie lifetime in seconds.
     *
     * @return \DateTimeInterface|int
     */
    protected function getCookieExpirationDate()
    {
        // only change expiration if using the cachettl driver
        if(($handler = $this->manager->driver()->getHandler()) instanceof CacheBasedSessionHandler) {
            if(!Auth::check())
                config(['session.lifetime' => 10]);

            $handler->setMinutes(config('session.lifetime'));
        }

        $config = $this->manager->getSessionConfig();
        $config['lifetime'] = config('session.lifetime');

        return $config['expire_on_close'] ? 0 : Date::instance(
            Carbon::now()->addRealMinutes($config['lifetime'])
        );
    }
}