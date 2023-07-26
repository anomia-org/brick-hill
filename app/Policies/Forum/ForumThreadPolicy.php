<?php

namespace App\Policies\Forum;

use App\Models\Forum\ForumThread;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class ForumThreadPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the thread.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\Forum\ForumThread  $forumThread
     * @return mixed
     */
    public function view(?User $user, ForumThread $forumThread)
    {
        return !$forumThread->deleted || $user?->can('manage forum');
    }
}
