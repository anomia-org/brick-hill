@extends('layouts.header')

@section('title', $board->name)

@section('content')
@auth
<div class="col-10-12 push-1-12">
    @include('includes.forumbar', ['bookmarks' => $bookmarks])
</div>
@endauth
<div class="col-10-12 push-1-12">
    {{--
        just so the create button doesnt show up on bookmarks/my posts
     --}}
    @if(auth()->check() && ($board->power ?? INF) <= (auth()->user()->power ?? 0))
    <div class="push-right mobile-col-1-1 pr0">
        <a class="button small {{ $board->category->color }}" href="/forum/{{ $board->id }}/create/">CREATE</a>
    </div>
    @endif
    <div class="forum-bar weight600" style="padding:10px 5px 10px 0;">
        <a href="/forum/">Forum</a> <i class="fa fa-angle-double-right" style="font-size:1rem;" aria-hidden="true"></i> <a href="/forum/{{ $board->id }}/">{{ $board->name }}</a>
    </div>
    <div class="card">
        <div class="top {{ $board->category->color }}">
            <div class="col-7-12">{{ $board->name }}</div>
            <div class="no-mobile overflow-auto topic text-center">
                <div class="col-3-12 stat">Replies</div>
                <div class="col-3-12 stat">Views</div>
                <div class="col-5-12"></div>
            </div>
        </div>
        <div class="content" style="padding: 0px">
            @foreach($threads as $thread)
            <div class="hover-card m0 thread-card {{ ($thread->viewed && Auth::check()) ? 'viewed' : '' }}">
                <div class="col-7-12 topic ellipsis">
                    @if($thread->pinned)
                    <span class="thread-label {{ $board->category->color }}">Pinned</span>
                    @endif
                    @if($thread->locked)
                    <span class="thread-label {{ $board->category->color }}">Locked</span>
                    @endif
                    @if($thread->deleted)
                    <span class="thread-label red">Deleted</span>
                    @endif
                    @if($thread->hidden)
                    @can('manage forum')
                    <span class="thread-label red">Hidden</span>
                    @endcan
                    @endif
                    <a href="/forum/thread/{{ $thread->id }}/"><span class="small-text label dark">{{ $thread->title }}</span></a><br>
                    <span class="label smaller-text">By <a href="/user/{{ $thread->author->id }}/" class="darkest-gray-text">{{ $thread->author->username }}</a></span>
                </div>
                <div class="no-mobile overflow-auto topic">
                    <div class="col-3-12 pt2 stat center-text">
                        <span class="title">{{ $thread->posts_count }}</span>
                    </div>
                    <div class="col-3-12 pt2 stat center-text">
                        <span class="title">{{ $thread->views }}</span>
                    </div>
                    <div class="col-6-12 post ellipsis text-right">
                        <span class="label dark small-text">{{ Helper::time_elapsed_string($thread->updated_at) }}</span><br>
                        <span class="label dark-gray-text smaller-text">By
                            <a class='darkest-gray-text' href="/user/{{ $thread->latestPost->author->id ?? $thread->author->id }}/">
                                {{ $thread->latestPost->author->username ?? $thread->author->username }}
                            </a>
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
            @if(count($threads) == 0)
            <div style="text-align:center;padding:10px;">
                Nothing here :(
            </div>
            @endif
        </div>
    </div>
    <div class="pages {{$board->category->color}}">
        @if(isset($pages['pages']))
            @foreach ($pages['pages'] as $pageNum)
                <a class="page {{ ($pages['current'] == $pageNum) ? 'active' : ''}}" href="/forum/{{ $board->id }}/{{ $pageNum }}">{{ $pageNum }}</a>
            @endforeach
        @elseif(is_numeric($pages))
            @php
            if(!request()->page)
                request()->request->set('page', 1);
            @endphp
            @if(request()->page > 1)
                <a class="page" href="/forum/{{ $board->id }}/{{ request()->page - 1 }}">{{ request()->page - 1 }}</a>
            @endif
            <a class="page active" href="/forum/{{ $board->id }}/{{ request()->page }}">{{ request()->page }}</a>
            @if(count($threads) == $pages)
                <a class="page" href="/forum/{{ $board->id }}/{{ request()->page + 1 }}">{{ request()->page + 1 }}</a>
            @endif
        @endif
    </div>
</div>
@endsection
