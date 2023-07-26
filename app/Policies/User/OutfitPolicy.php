<?php

namespace App\Policies\User;

use App\Models\User\Outfit;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OutfitPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\User\Outfit  $outfit
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Outfit $outfit)
    {
        return $user->id == $outfit->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\User\Outfit  $outfit
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Outfit $outfit)
    {
        return $user->id == $outfit->user_id;
    }
}
