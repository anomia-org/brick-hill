<?php

namespace App\Http\Controllers\Admin\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Polymorphic\Comment;

class PolymorphicController extends Controller
{
    public function scrubComment(Comment $comment) {
        $comment->scrubbed = !$comment->scrubbed;
        $comment->save();

        return $comment->only('scrubbed');
    }
}
