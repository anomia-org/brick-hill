<?php

namespace App\Traits\Controllers;

use Illuminate\Support\Facades\{
    Auth,
    Cache
};

use Carbon\Carbon;

trait PostLimit
{
    /**
     * Returns if the user has created a new row for the relationship in the given timeframe
     *
     * @param  mixed $relationship
     * @param  integer $timeLimit
     * @return boolean
     */
    public function canMakeNewPost(mixed $relationship, int $timeLimit = 30): bool
    {
        $lock = Cache::lock("user_" . Auth::id() . '_post_lock', 5);

        try {
            $lock->block(5);

            return !$relationship
                ->where('created_at', '>=', Carbon::now()->subSeconds($timeLimit))
                ->exists();
        } finally {
            optional($lock)->release();
        }
    }
}
