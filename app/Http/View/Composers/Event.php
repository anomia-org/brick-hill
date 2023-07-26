<?php

namespace App\Http\View\Composers;

use App\Constants\EventNumber;
use App\Models\Event\EventProgress;
use Illuminate\View\View;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Event
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if ((!request()->ajax() && !request()->wantsJson()) && Auth::check()) {
            /*$chance = rand(0, 500);

            if ($chance == 1) {
                $key = Str::random(32);

                $masks = [
                    1 => 0b0001,
                    2 => 0b0010,
                    3 => 0b0100,
                    4 => 0b1000
                ];

                $get_progress = EventProgress::firstOrNew(
                    ['user_id' => Auth::id(), 'event_id' => EventNumber::ORNAMENTS_2022]
                );

                $possible = [];

                foreach ($masks as $m_key => $mask) {
                    if (!($get_progress->stage & $mask)) {
                        $possible[] = $m_key;
                    }
                }

                session(['event_key' => $possible[array_rand($possible)] . $key]);

                $view->with('event_seen', 1);
            }*/
        }
    }
}
