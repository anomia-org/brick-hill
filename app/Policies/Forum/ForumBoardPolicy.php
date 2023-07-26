<?php

namespace App\Policies\Forum;

use App\Models\Forum\ForumBoard;
use App\Models\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ForumBoardPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can access the board
     *
     * @param  \App\Models\User\User  $user
     * @param  \App\Models\Forum\ForumBoard  $forumBoard
     * @return mixed
     */
    public function view(?User $user, ForumBoard $forumBoard)
    {
        return $forumBoard->power <= optional($user)->power;
    }
}
