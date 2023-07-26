<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Artisan;

class MigrationsEnded
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Artisan::call('db:load-state');
    }
}
