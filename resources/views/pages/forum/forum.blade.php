@extends('layouts.header')

@section('title', 'Forum')

@section('content')
@auth
    @include('includes.forumbar', ['bookmarks' => $bookmarks])
@endauth
<div class="col-8-12">
    @foreach($boards as $category)
    <div class="card">
        <div class="top {{ $category[0]->category->color }}">
            <div class="col-7-12">{{ $category[0]->category->title }}</div>
            <div class="no-mobile overflow-auto topic text-center">
                <div class="col-3-12 stat">Threads</div>
                <div class="col-3-12 stat">Replies</div>
                <div class="col-6-12"></div>
            </div>
        </div>
        <div class="content">
        @foreach($category as $board)
            <div class="board-info mb1">
                <div class="col-7-12 board">
                    <div><a class="label dark" href="/forum/{{ $board->id }}">{{ $board->name }}</a></div>
                    <span class="label small">{{ $board->description }}</span>
                </div>
                <div class="no-mobile overflow-auto board ellipsis" style="overflow:hidden;">
                    <div class="col-3-12 stat">
                        <span class="title">{{ $board->thread_count_cached }}</span>
                    </div>
                    <div class="col-3-12 stat">
                        <span class="title">{{ $board->post_count_cached }}</span>
                    </div>
                    <div class="col-6-12 text-right ellipsis pt2" style="max-width:180px;">
                        <a href="/forum/thread/{{ $board->latestThread?->id }}/" class="label dark">{{ $board->latestThread?->title }}</a><br>
                        <span class="label small">{{ Helper::time_elapsed_string($board->latestThread?->updated_at) }}</span>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    </div>
    @endforeach
</div>
<div class="col-4-12">
    <div class="card">
        <div class="top">
            Recent Topics
        </div>
        <div class="content">
            @foreach($recent as $thread)
            <div class="thread">
                <div class="col-10-12 ellipsis">
                    <div class="ellipsis mb1">
                        <a class="label dark" href="/forum/thread/{{ $thread->id }}/">{{ $thread->title }}</a>
                    </div>
                    <div class="label small ellipsis">
                        by <a href="/user/{{ $thread->author->id }}/" class="dark-gray-text">{{ $thread->author->username }}</a> in <a class="dark-gray-text" href="/forum/{{ $thread->board->id }}/">{{ $thread->board->name }}</a>
                    </div>
                </div>
                <div class="col-2-12">
                    <div class="forum-tag">{{ $thread->posts_count }}</div>
                </div>
            </div>
            @if(!$loop->last)
            <hr>
            @endif
            @endforeach
        </div>
    </div>
    <div class="card">
        <div class="top">
            Popular Topics
        </div>
        <div class="content">
            @foreach($popular as $thread)
                <div class="thread">
                    <div class="col-10-12 ellipsis">
                        <div class="ellipsis mb1">
                        <a class="label dark" href="/forum/thread/{{ $thread->id }}/">{{ $thread->title }}</a>
                        </div>
                        <div class="label small ellipsis">
                        by <a href="/user/{{ $thread->author->id }}/" class="dark-gray-text">{{ $thread->author->username }}</a> in <a class="dark-gray-text" href="/forum/{{ $thread->board->id }}/">{{ $thread->board->name }}</a>
                        </div>
                    </div>
                    <div class="col-2-12">
                        <div class="forum-tag">{{ $thread->posts_count }}</div>
                    </div>
                </div>
                @if(!$loop->last)
                <hr>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endsection
