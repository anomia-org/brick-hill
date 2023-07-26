<div class="col-8-12">
    <div class="card forum-links inline">
        <div class="content">
            <div class="inline">
                <a href="/rules">Rules</a>
                <span class="divide"></span>
                <div class="inline">
                    <a href="/forum/bookmarks">Bookmarked
                        @if($bookmarks > 0)
                        <span class="nav-notif" style="margin-left:-10px">{{ $bookmarks }}</span>
                        @endif
                    </a>
                </div>
                <a href="/forum/my_posts">My Posts</a>
                <a href="/forum/drafts">Drafts</a>
            </div>
        </div>
    </div>
</div>
{{--
    still coming soon
    <div class="col-4-12">
        <form action="{{ route('searchForum') }}">
            <input style="padding:11.75px;border-radius:5px;" type="text" class="search" name="search" placeholder="Search Forum">
        </form>
    </div>
--}}
