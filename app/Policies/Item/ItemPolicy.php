<?php

namespace App\Policies\Item;

use App\Models\Item\Item;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\Item\Item  $item
     * @return mixed
     */
    public function view(?User $user, Item $item)
    {
        return $item->is_public || $user?->can('manage shop');
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
     * @param  \App\Models\Item\Item  $item
     * @return mixed
     */
    public function update(User $user, Item $item)
    {
        return $item->creator_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\Item\Item  $item
     * @return mixed
     */
    public function updateOfficial(User $user, Item $item)
    {
        return $item->creator_id == config('site.main_account_id') && $user->can('manage shop');
    }
}
