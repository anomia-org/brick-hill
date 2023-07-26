<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\User\{
    Ban,
    User,
    Trade,
    Status,
    BanType,
    Message,
    PastUsername,
    Admin\Report
};
use App\Models\Item\Item;
use App\Models\Polymorphic\{
    Comment,
};
use App\Models\Forum\{
    ForumPost,
    ForumThread
};
use App\Models\Clan\Clan;

class AdminUserController extends Controller
{
    public function scrubStatus(User $user)
    {
        $status = Status::latest('id')->userId($user->id)->first();
        $status->scrubbed = !$status->scrubbed;
        $status->save();

        return back();
    }

    public function scrubDesc(User $user)
    {
        $user->desc_scrubbed = !$user->desc_scrubbed;
        $user->save();

        return back();
    }

    public function renderAvatar(User $user)
    {
        $user->thumbnails()->update([
            'expires_at' => now()
        ]);

        return back();
    }

    public function banUser(User $user)
    {
        if ($user->bans()->active()->exists())
            return redirect("/user/$user->id/unban")
                ->withErrors(['User is already banned. Review their current ban and either leave it or unban them and extend it.']);

        $bans = Ban::userId($user->id)
            ->orderBy('id', 'DESC')
            ->get();
        $banTypes = BanType::all();

        $ban_content = null;

        $type = request('type');
        $content_id = request('content');
        if ($type && $content_id)
            switch ($type) {
                case 'forumthread':
                    $content = ForumThread::find($content_id);
                    if ($content)
                        $ban_content = $content->title . ": " . $content->body;
                    break;
                case 'forumpost':
                    $content = ForumPost::find($content_id);
                    if ($content)
                        $ban_content = $content->body;
                    break;
                case 'item':
                    $content = Item::find($content_id);
                    if ($content)
                        $ban_content = config('site.url') . "/shop/" . $content_id;
                    break;
                case 'clan':
                    $content = Clan::find($content_id);
                    if ($content)
                        $ban_content = config('site.url') . "/clan/" . $content_id;
                    break;
                case 'comment':
                    $content = Comment::find($content_id);
                    if ($content)
                        $ban_content = $content->comment;
                    break;
                case 'message':
                    $content = Message::find($content_id);
                    if ($content)
                        $ban_content = $content->title . ': ' . $content->message;
                    break;
            }

        return view('pages.admin.ban')->with([
            'ban_history' => $bans,
            'types' => $banTypes,
            'ban_content' => $ban_content
        ]);
    }

    public function unbanUser(User $user)
    {
        if (!$user->bans()->active()->exists())
            return redirect()
                ->route('admin');
        $bans = Ban::userId($user->id)
            ->orderBy('id', 'DESC')
            ->get();
        return view('pages.admin.ban')->with([
            'ban_history' => $bans
        ]);
    }

    public function superban(User $user)
    {
        return view('pages.admin.ban')->with([
            'superban' => true
        ]);
    }

    public function postBan(User $user)
    {
        $id = $user->id;
        $banTypes = BanType::all()->toKey('id')->toArray();
        $validLengths = [
            0,
            60,
            720,
            1440,
            4320,
            10080,
            43200,
            129600,
            525600,
            37317600
        ];
        if (request('unban') !== null) {
            if (!$user->bans()->active()->exists())
                return redirect()
                    ->route('admin');
            $user->bans()->update([
                'active' => 0
            ]);
        } elseif (request('superban') !== null) {
            if ($user->bans()->active()->exists())
                $user->bans()->update([
                    'active' => 0
                ]);
            Ban::create([
                'user_id' => $id,
                'admin_id' => Auth::id(),
                'note' => 'Do not sign up to Brick Hill with intent on breaking our rules.',
                'length' => 37317600,
                'active' => 1
            ]);
            ForumThread::where('author_id', $id)
                ->update([
                    'deleted' => 1
                ]);
            ForumPost::where('author_id', $id)
                ->update([
                    'scrubbed' => 1
                ]);
            Comment::where('author_id', $id)
                ->update([
                    'scrubbed' => 1
                ]);
            Message::where('author_id', $id)
                ->update([
                    'title' => '[ Content Removed ]',
                    'message' => '[ Content Removed ]',
                    'read' => 1
                ]);
            Report::where('user_id', $id)
                ->update([
                    'seen' => 1
                ]);
            Status::where('owner_id', $id)
                ->update([
                    'scrubbed' => 1
                ]);
            $new = PastUsername::create([
                'user_id' => $id,
                'old_username' => $user->username,
                'new_username' => "[Deleted$id]",
                'hidden' => 1
            ]);
            $user->username = "[Deleted$id]";
            $user->desc_scrubbed = true;
            $user->save();

            $admin = Auth::user();
            $admin->increment('admin_points', 1);
            $admin->save();
        } else {
            if ($user->bans()->active()->exists() || !in_array(request('type'), $banTypes) || !in_array(request('length'), $validLengths))
                return redirect()
                    ->route('admin');
            if (request('note') === null)
                return error('Note must be filled in');
            Ban::create([
                'user_id' => $id,
                'admin_id' => Auth::id(),
                'note' => request('note'),
                'content' => request('content'),
                'ban_type_id' => request('type'),
                'length' => request('length'),
                'active' => 1
            ]);
            $admin = Auth::user();
            $admin->increment('admin_points', 1);
            $admin->save();

            if (request()->has(['content_type', 'content_id']))
                switch (request('content_type')) {
                    case 'forumthread':
                        $thread = ForumThread::findOrFail(request('content_id'));
                        $thread->timestamps = false;
                        $thread->deleted = 1;
                        $thread->save();
                        return redirect()
                            ->route('thread', ['thread' => request('content_id')]);
                    case 'forumpost':
                        $post = ForumPost::findOrFail(request('content_id'));
                        $post->timestamps = false;
                        $post->scrubbed = 1;
                        $post->save();
                        return redirect()
                            ->route('thread', ['thread' => $post->thread->id]);
                    case 'item':
                        return redirect()
                            ->route('admin');
                    case 'clan':
                        return redirect()
                            ->route('admin');
                    case 'comment':
                        $comment = Comment::findOrFail(request('content_id'));
                        $comment->scrubbed = 1;
                        $comment->save();
                        return redirect()
                            ->route('admin');
                    case 'message':
                        $message = Message::findOrFail(request('content_id'));
                        $message->title = '[ Content Removed ]';
                        $message->message = '[ Content Removed ]';
                        $message->timestamps = false;
                        $message->save();
                        return redirect()
                            ->route('admin');
                }
        }
        return redirect()
            ->route('profilePage', ['id' => $id]);
    }

    public function userAudit(User $user)
    {
        $user->load('emails', 'bans.admin', 'payments');

        return view('pages.admin.audit')->with([
            'user' => $user,
            'emails' => $user->emails,
            'bans' => $user->bans
        ]);
    }

    public function viewBan(User $user, Ban $ban)
    {
        if ($ban->user_id != $user->id)
            return abort(403);
        return view('pages.user.banned')->with([
            'ban' => $ban,
            'past' => false
        ]);
    }

    public function scrubMessage(Message $message)
    {
        $message->title = '[ Content Removed ]';
        $message->message = '[ Content Removed ]';
        $message->timestamps = false;
        $message->save();

        return back();
    }

    public function scrubUsername(User $user)
    {
        $id = $user->id;
        $past = PastUsername::userId($id)
            ->hidden()
            ->newName($user->username)
            ->orderBy('created_at', 'DESC')
            ->first();
        if (!$past || $user->username != "[Deleted$id]") {
            $new = PastUsername::create([
                'user_id' => $id,
                'old_username' => $user->username,
                'new_username' => "[Deleted$id]",
                'hidden' => 1
            ]);
            $user->username = "[Deleted$id]";
            $user->save();

            return redirect()
                ->route('banUser', ['user' => $id])
                ->with("success", "Username scrubbed, now terminate the user.");
        } else {
            // is it better to check if their username is still	[Deleted$id] or if they changed their username after the scrub
            if ($user->username == "[Deleted$id]") {
                $new = PastUsername::create([
                    'user_id' => $id,
                    'old_username' => "[Deleted$id]",
                    'new_username' => $past->old_username,
                    'hidden' => 1
                ]);
                $user->username = $past->old_username;
                $user->save();

                return back();
            }
        }
        return back();
    }
}
