<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\{
    Auth,
    Cache,
    Redis
};

use Carbon\Carbon;
use App\Helpers\Helper;

use App\Models\Forum\{
    ForumPost,
    ForumBoard,
    ForumDraft,
    ForumThread,
    ForumBookmark
};

class ForumController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except('index', 'thread', 'board', 'searchForum');

        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $bookmarks = ForumBookmark::active()
                    ->unseen()
                    ->userId(Auth::id())
                    ->whereHas('thread', fn ($q) => $q->where([['deleted', 0], ['hidden', 0]]))
                    ->count();

                view()->share('bookmarks', $bookmarks);
            }

            return $next($request);
        });
    }

    public function index()
    {
        $boards = ForumBoard::with('category', 'latestThread')
            ->whereNotNull('category_id')
            ->get()
            ->each
            ->append('post_count_cached', 'thread_count_cached')
            ->mapToGroups(function ($board, $key) {
                return [$board['category']['id'] => $board];
            })->sortBy(function ($board, $key) {
                return $board[0]['category']['order'];
            });

        $recent = ForumThread::today()
            ->recent()
            ->whereHas('board')
            ->with('author', 'board.category')
            ->withCount('posts')
            ->limit(5)
            ->get();

        $popular = Cache::remember('forumPopularPosts', rand(5, 15) * 60, function () {
            return ForumThread::today()
                ->popular()
                ->whereHas('board')
                ->with('author', 'board.category')
                ->limit(5)
                ->get();
        });

        //$recent = [];
        //$popular = [];

        return view('pages.forum.forum')->with([
            'boards' => $boards,
            'recent' => $recent,
            'popular' => $popular
        ]);
    }

    public function thread(ForumThread $thread, $page = 1)
    {
        $thread->load(['posts' => function ($q) use ($page) {
            $q->limit(10)
                ->offset(($page * 10) - 10)
                ->with('author.primaryClan');
        }, 'board.category', 'author.primaryClan'])
            ->loadCount('posts');

        if ($thread->board->power > optional(Auth::user())->power)
            return redirect('forum');

        $replies = $thread
            ->posts
            ->each
            ->append('quotes');

        $pageCount = ceil($thread->posts_count / 10);
        $pages = Helper::paginate(10, $page, 10, $pageCount);

        $hasBookmarked = !!ForumBookmark::threadId($thread->id)->active()->userId(Auth::id())->first();

        $this->addView($thread->id);

        $boards = [];
        if (Auth::user()?->is_admin)
            $boards = ForumBoard::all();

        return view('pages.forum.thread')->with([
            'replies' => $replies,
            'thread' => $thread,
            'boards' => $boards,
            'pages' => $pages,
            'bookmarked' => $hasBookmarked
        ]);
    }

    public function board(ForumBoard $board, $page = 1)
    {
        $threads = ForumThread::inBoard($board->id)->notDeleted(optional(Auth::user())->power)
            ->with('author', 'latestPost.author')
            ->withCount('posts')
            ->limit(20)
            ->offset(($page * 20) - 20)
            ->orderBy('pinned', 'DESC')
            ->orderBy('updated_at', 'DESC')
            ->get();

        $pageCount = ceil($board->thread_count_cached / 20);
        $pages = Helper::paginate(20, $page, 10, $pageCount);

        $board->load('category');

        return view('pages.forum.board')->with([
            'board' => $board,
            'threads' => $threads,
            'pages' => $pages
        ]);
    }

    public function viewDraft(ForumDraft $draft)
    {
        return view('pages.forum.drafts.draft')->with([
            'draft' => $draft
        ]);
    }

    public function myDrafts($page = 1)
    {
        $q = ForumDraft::userId(Auth::id())
            ->notDeleted()
            ->orderBy('updated_at', 'DESC')
            ->with('board.category');

        $draftCount = $q->count();
        $drafts = $q
            ->limit(20)
            ->offset(($page * 20) - 20)
            ->get();

        $pages = Helper::paginate(20, $page, 10, ceil($draftCount / 20));

        return view('pages.forum.drafts.drafts')->with([
            'drafts' => $drafts,
            'pages' => $pages
        ]);
    }

    public function myPosts($page = 1)
    {
        $threads = ForumThread::with('author', 'latestPost.author')
            ->withCount('posts')
            ->where('author_id', Auth::id())
            ->withoutGlobalScope('hidden')
            ->union(
                ForumThread::whereIn('id', function ($q) {
                    $q->select('thread_id')->from('forum_posts')->where('author_id', Auth::id());
                })
                    ->withCount('posts')
                    ->withoutGlobalScope('hidden')
                    ->notDeleted()
            )
            ->orderBy('updated_at', 'DESC')
            ->notDeleted()
            ->offset(($page * 20) - 20)
            ->limit(20)
            ->get();

        return view('pages.forum.board')->with([
            'board' => (object) [
                'category' => (object) [
                    'color' => 'blue'
                ],
                'name' => 'My Posts',
                'id' => 'my_posts'
            ],
            'threads' => $threads,
            'pages' => 20
        ]);
    }

    public function myBookmarks($page = 1)
    {
        $q = ForumBookmark::userId(Auth::id())
            ->active()
            ->join('forum_threads as th', 'th.id', '=', 'forum_bookmarks.thread_id')
            ->where([['th.deleted', 0], ['th.hidden', 0]])
            ->orderBy('th.updated_at', 'DESC')
            ->select('forum_bookmarks.*')
            ->with('thread.author');

        $bookmarkCount = $q->count();
        $bookmarks = $q
            ->withCount('posts')
            ->limit(20)
            ->offset(($page * 20) - 20)
            ->get()
            ->map(function ($bookmark, $key) {
                $bookmark->thread->viewed = $bookmark->seen;
                $bookmark->thread->posts_count = $bookmark->posts_count;
                return $bookmark->thread;
            });

        $pages = Helper::paginate(20, $page, 10, ceil($bookmarkCount / 20));

        return view('pages.forum.board')->with([
            'board' => (object) [
                'category' => (object) [
                    'color' => 'blue'
                ],
                'name' => 'My Bookmarks',
                'id' => 'bookmarks'
            ],
            'threads' => $bookmarks,
            'pages' => $pages
        ]);
    }

    public function threadPageFromPost(ForumPost $post)
    {
        return redirect()
            ->route('thread', ['thread' => $post->thread->id, 'page' => $post->page_number, '#post' . $post->id]);
    }

    private function addView($id)
    {
        if (Auth::check()) {
            $thread = ForumThread::findOrFail($id);
            $hasViewed = $thread->viewed;

            $bookmark = ForumBookmark::threadId($id)->active()->unseen()->userId(Auth::id())
                ->first();
            if ($bookmark) {
                $bookmark->timestamps = false;
                $bookmark->seen = true;
                $bookmark->save();
            }

            if (!$hasViewed) {
                $views = Redis::incr("thread:$thread->id:view_count");
                Redis::expire("thread:$thread->id:view_count", 86400);
                if ($views >= 5) {
                    Redis::setex("thread:$thread->id:view_count", 86400, "0");
                    $thread->timestamps = false;
                    $thread->views = $thread->views + $views;
                    $thread->save();
                }

                $views = Auth::user()->viewed_threads;
                $newArr[$id] = Carbon::now()->timestamp;
                $views = $newArr + $views;
                $uid = Auth::id();
                Redis::setex("user:{$uid}:threads:views", 86400 * 7 /* 7 days */, json_encode($views));
            }
        }
    }
}
