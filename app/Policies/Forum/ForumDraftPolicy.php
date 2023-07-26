<?php

namespace App\Policies\Forum;

use App\Models\Forum\ForumDraft;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ForumDraftPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\Forum\ForumDraft  $forumDraft
     * @return mixed
     */
    public function view(User $user, ForumDraft $forumDraft)
    {
        return !$forumDraft->deleted && $forumDraft->user_id == $user->id;
    }
}
