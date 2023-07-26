<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Forum\{
    ForumPost,
    ForumBoard,
    ForumThread
};
use App\Models\User\{
    BanType,
    Ban
};

use App\Http\Requests\General\Toggle;

class AdminForumController extends Controller
{
    public function scrubPost(Toggle $request, ForumPost $post) {
        $post->timestamps = false;
        $post->scrubbed = $request->toggle;
        $post->save();

        return back();
    }

    public function scrubThread(Toggle $request, ForumThread $thread) {
        $thread->timestamps = false;
        $thread->scrubbed = $request->toggle;
        $thread->save();

        return back();
    }

    public function lockThread(Toggle $request, ForumThread $thread) {
        $thread->timestamps = false;
        $thread->locked = $request->toggle;
        $thread->save();

        return back();
    }

    public function deleteThread(Toggle $request, ForumThread $thread) {
        $thread->timestamps = false;
        $thread->deleted = $request->toggle;
        $thread->save();

        return back();
    }

    public function pinThread(Toggle $request, ForumThread $thread) {
        $thread->timestamps = false;
        $thread->pinned = $request->toggle;
        $thread->save();

        return back();
    }

    public function hideThread(Toggle $request, ForumThread $thread) {
        $thread->timestamps = false;
        $thread->hidden = $request->toggle;
        $thread->save();

        return back();
    }

    public function moveThread(Request $request, ForumThread $thread) {
        $board = ForumBoard::findOrFail($request->location);
        $thread->timestamps = false;
        $thread->board_id = $board->id;
        $thread->save();

        if($request->warn && !$thread->author->bans()->active()->exists()) {
            $incorrect_subforum = BanType::find(13);
            Ban::create([
                'user_id' => $thread->author_id,
                'admin_id' => auth()->id(),
                'note' => $incorrect_subforum->default_note,
                'ban_type_id' => $incorrect_subforum->id,
                'length' => $incorrect_subforum->default_length,
                'active' => 1
            ]);
            Auth::user()->increment('admin_points', 1);
        }

        return back();
    }
}
