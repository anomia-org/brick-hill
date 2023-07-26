@extends('layouts.header')

@section('title', 'Create Reply')

@section('content')
@auth
<div class="col-10-12 push-1-12">
    @include('includes.forumbar', ['bookmarks' => $bookmarks])
</div>
@endauth
<div class="col-10-12 push-1-12">
    <div class="forum-bar weight600" style="padding:10px 5px 10px 0;">
        <a href="/forum/">Forum</a>
        <i class="fa fa-angle-double-right" style="font-size:1rem;" aria-hidden="true"></i>
        <a href="/forum/{{ $board->id }}/">{{ $board->name }}</a>
        <i class="fa fa-angle-double-right" style="font-size:1rem;" aria-hidden="true"></i>
        <a href="/forum/thread/{{ $thread->id }}/">
            <span class="weight700 very-bold">
                {{ $thread->title }}
            </span>
        </a>
    </div>
    <div class="card">
        <div class="top {{ $board->category->color }}">
            Reply to {{ $thread->title }}
        </div>

        <div class="content">
            <form method="POST" id="post-form">
                @csrf
                <textarea id="body" name="body" placeholder="Body (max 1,000 characters)" style="width:100%;min-height:200px;font-size:16px;box-sizing:border-box;margin-top:10px;" required>{{ old('body') }}</textarea>
                <div style="text-align:center;">
                    <button type="submit" class="button small-text {{ $board->category->color }}">
                        Create Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
