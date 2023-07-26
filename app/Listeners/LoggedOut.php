<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Google2FA;

class LoggedOut
{
    /**
     * Handle the event.
     *
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        // extra precaution incase some idiot calls auth logout in a different spot
        // without ending the 2fa session
        Google2FA::logout();
    }
}
