<?php

namespace App\Policies\User;

use App\Models\User\Trade;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TradePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\User\Trade  $trade
     * @return mixed
     */
    public function view(User $user, Trade $trade)
    {
        return $user->can('view user economy') || $user->id == $trade->sender_id || $user->id == $trade->receiver_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\User\Trade  $trade
     * @return mixed
     */
    public function update(User $user, Trade $trade)
    {
        //
    }
}
