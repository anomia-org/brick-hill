<?php

namespace App\Extensions\Session;

use Illuminate\Session\CacheBasedSessionHandler as OriginalCacheBasedSessionHandler;

class CacheBasedSessionHandler extends OriginalCacheBasedSessionHandler
{
    /**
     * Set minutes until expiration
     * 
     * @param mixed $minutes 
     * @return void 
     */
    public function setMinutes($minutes) {
        $this->minutes = $minutes;
    }
}