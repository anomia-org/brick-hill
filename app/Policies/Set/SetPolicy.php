<?php

namespace App\Policies\Set;

use App\Models\Set\Set;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\Models\Membership\Membership;

class SetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\Set\Set  $set
     * @return mixed
     */
    public function update(User $user, Set $set)
    {
        return $set->creator_id == $user->id;
    }

    /**
     * Determine whether the user can view or update the host key
     * 
     * @param \App\Models\User\User $user 
     * @param \App\Models\Set\Set $set 
     * @return bool 
     */
    public function updateHostKey(User $user, Set $set)
    {
        return $set->creator_id == $user->id && !$set->is_dedicated;
    }

    /**
     * Determine whether the user can make their set run on dedicated servers
     * 
     * @param \App\Models\User\User $user 
     * @param \App\Models\Set\Set $set 
     * @return \Illuminate\Auth\Access\Response
     */
    public function updateDedicated(User $user, Set $set)
    {
        return $set->creator_id == $user->id && (Membership::userId($user->id)->exists() || $user->is_admin || $user->is_beta_tester)
            ? Response::allow()
            : Response::deny("You must have membership to active a dedicated server");
    }
}
