<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use App\Models\Forum\{
    ForumPost,
    ForumDraft,
    ForumBoard,
    ForumThread,
    ForumBookmark
};

use App\Notifications\{
    SuspiciousThread,
    SuspiciousReply
};

class CreateController extends Controller
{
    use \App\Traits\Controllers\PostLimit;

    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $bookmarks = ForumBookmark::active()
                ->unseen()
                ->userId(Auth::id())
                ->whereHas('thread', fn ($q) => $q->where([['deleted', 0], ['hidden', 0]]))
                ->count();

            view()->share('bookmarks', $bookmarks);

            return $next($request);
        });

        $this->middleware(function ($request, $next) {
            if (!Carbon::parse(Auth::user()->created_at)->addDays(3)->isPast())
                return error('Your account must be at least three days old to make forum posts');

            if (!Auth::user()->email || !Auth::user()->email->verified)
                return error('You must have a verified email to post on the forums');

            return $next($request);
        })->except([
            'bookmark'
        ]);
    }

    public function createThreadPage(ForumBoard $board)
    {
        return view('pages.forum.create.thread')->with([
            'board' => $board
        ]);
    }

    public function createReplyPage(ForumThread $thread)
    {
        if ($thread->locked || $thread->deleted || $thread->board->power > optional(Auth::user())->power) return redirect('forum');

        return view('pages.forum.create.reply')->with([
            'thread' => $thread,
            'board' => $thread->board
        ]);
    }

    public function createReply(ForumThread $thread)
    {
        $rules = [
            'body' => 'required|string|max:1000|min:7'
        ];

        $validator = validator(request()->all(), $rules);

        if ($validator->fails())
            return error($validator->errors());

        if ($thread->locked || $thread->deleted || $thread->board->power > optional(Auth::user())->power)
            return redirect('forum');
        if (Carbon::parse($thread->updated_at)->addMonth()->isPast() && Auth::user()->power == 0)
            return error('You cannot post on threads with one month of inactivity');

        if (!$this->canMakeNewPost(Auth::user()->userPosts(), 15))
            throw new \BaseException('You can only create one post every 15 seconds');

        $newReply = ForumPost::create([
            'author_id' => Auth::id(),
            'thread_id' => $thread->id,
            'body' => request('body')
        ]);

        $pageCount = max(ceil(ForumPost::threadId($thread->id)->count() / 10), 1);

        if (SuspiciousInput(request('body')))
            Auth::user()->notify(new SuspiciousReply($newReply, $pageCount));

        $thread->touch();

        ForumBookmark::active()->threadId($thread->id)->update(['seen' => 0]);

        return redirect()->route('thread', ['thread' => $thread->id, 'page' => $pageCount, "#post$newReply->id"]);
    }

    public function createQuotePage(ForumThread $thread, ForumPost $post)
    {
        if ($post->thread_id != $thread->id)
            return redirect()
                ->route('thread', ['id' => $thread->id]);

        $post->load('thread.board.category', 'author')->append('quotes');

        if ($thread->locked || $thread->deleted || $thread->board->power > optional(Auth::user())->power) return redirect('forum');

        return view('pages.forum.create.quote')->with([
            'reply' => $post,
            'thread' => $thread,
            'board' => $thread->board
        ]);
    }

    public function createQuote(ForumThread $thread, ForumPost $post)
    {
        if ($post->thread_id != $thread->id)
            return redirect()
                ->route('thread', ['id' => $thread->id]);

        $rules = [
            'body' => 'required|string|max:1000|min:7'
        ];

        $validator = validator(request()->all(), $rules);

        if ($validator->fails())
            return error($validator->errors());

        if ($thread->locked || $thread->deleted || $thread->board->power > optional(Auth::user())->power)
            return redirect('forum');
        if (Carbon::parse($thread->updated_at)->addMonth()->isPast() && Auth::user()->power == 0)
            return error('You cannot post on threads with one month of inactivity');

        if (!$this->canMakeNewPost(Auth::user()->userPosts(), 15))
            throw new \BaseException('You can only create one post every 15 seconds');

        $newReply = ForumPost::create([
            'author_id' => Auth::id(),
            'thread_id' => $thread->id,
            'quote_id' => $post->id,
            'body' => request('body')
        ]);

        $pageCount = max(ceil(ForumPost::threadId($thread->id)->count() / 10), 1);

        if (SuspiciousInput(request('body')))
            Auth::user()->notify(new SuspiciousReply($newReply, $pageCount));

        $thread->touch();

        return redirect()->route('thread', ['thread' => $thread->id, 'page' => $pageCount, "#post$newReply->id"]);
    }

    public function createThread(ForumBoard $board, $draftId = 0)
    {
        $rules = [
            'title' => 'required|string|max:60|min:5',
            'body' => 'required|string|max:3000|min:10'
        ];

        $validator = validator(request()->all(), $rules);

        if ($validator->fails())
            return error($validator->errors());

        if (!$this->canMakeNewPost(Auth::user()->userThreads(), 60))
            throw new \BaseException('You can only create one thread every minute');

        if ($draftId > 0) {
            $draft = ForumDraft::userId(Auth::id())->findOrFail($draftId);
            $draft->deleted = 1;
            $draft->save();
        }

        $suspicious = SuspiciousInput(request('body'));

        $newThread = ForumThread::create([
            'author_id' => Auth::id(),
            'board_id' => $board->id,
            'title' => request('title'),
            'body' => request('body'),
            'hidden' => $suspicious
        ]);

        if ($suspicious)
            Auth::user()->notify(new SuspiciousThread($newThread));

        return redirect()->route('thread', ['thread' => $newThread->id]);
    }

    public function bookmark(ForumThread $thread)
    {
        if ($thread->board->power > optional(Auth::user())->power) return redirect('forum');

        $check = ForumBookmark::userId(Auth::id())
            ->threadId($thread->id)
            ->first();
        if (!$check) {
            ForumBookmark::create([
                'user_id' => Auth::id(),
                'thread_id' => $thread->id,
                'seen' => 1,
                'active' => 1
            ]);
        } else {
            $check->active = !$check->active;
            $check->save();
        }

        return redirect()->route('thread', ['thread' => $thread->id]);
    }


    public function createDraft(ForumBoard $board)
    {
        $rules = [
            'title' => 'required|string|max:60|min:5',
            'body' => 'required|string|max:3000|min:10'
        ];

        $validator = validator(request()->all(), $rules);

        if ($validator->fails())
            return error($validator->errors());

        $lastDraft = ForumDraft::userId(Auth::id())
            ->where('created_at', '>=', Carbon::now()->subMinute());

        if ($lastDraft->count() > 0 && Auth::user()->power == 0)
            return error('You can only create one draft every minute');

        $newDraft = ForumDraft::create([
            'user_id' => Auth::id(),
            'board_id' => $board->id,
            'title' => request('title'),
            'body' => request('body')
        ]);

        return redirect(route('draft', ['draft' => $newDraft->id]));
    }

    public function updateDraft(ForumDraft $draft)
    {
        $rules = [
            'title' => 'required|string|max:60|min:5',
            'body' => 'required|string|max:3000|min:10'
        ];

        $validator = validator(request()->all(), $rules);

        if ($validator->fails())
            return error($validator->errors());

        $draft->title = request('title');
        $draft->body = request('body');

        $draft->save();

        return redirect(route('draft', ['draft' => $draft->id]));
    }

    public function deleteDraft(ForumDraft $draft)
    {
        $draft->deleted = 1;
        $draft->save();

        return redirect()->route('drafts');
    }
}
