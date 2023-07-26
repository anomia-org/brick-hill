<?php

namespace App\Policies\User;

use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     * Only applied to admin routes related to viewing.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\User\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        return $user->is_admin && ($user->power > $model->power || $user->is($model) || $model->id == config('site.main_account_id'));
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\User\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        return $user->is_admin && ($user->power > $model->power || $user->is($model));
    }
}
