@extends('layouts.header')

@section('title', 'Create Quote')

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
            <span class="weight700 bold">
                {{ $thread->title }}
            </span>
        </a>
    </div>
    <div class="card">
        <div class="top {{ $board->category->color }}">
            Reply to {{ $thread->title }}
        </div>

        <div class="content">
            <blockquote class="{{ $board->category->color }}">
                <em>Quote from
                    <a style="color:#444" href="https://www.brick-hill.com/user/{{ $reply->author->id }}">
                        {{ $reply->author->username }}
                    </a>, {{ \Carbon\Carbon::parse($reply->created_at)->format('h:i A d/m/Y') }}
                </em>
                @if($reply->quotes)
                @foreach($reply->quotes as $quote)
                    <blockquote class="{{ $thread->board->category->color }}">
                        <em>Quote from
                            <a style="color:#444" href="https://www.brick-hill.com/user/{{ $quote->author->id }}">
                                {{ $quote->author->username }}
                            </a>, {{ Carbon\Carbon::parse($quote->created_at)->format('h:i A d/m/Y') }}
                        </em>
                @endforeach
                @foreach(array_reverse($reply->quotes) as $quote)
                        <br>
                        {!! nl2br(Helper::bbcode_to_html(e($quote->body), e($quote->author->power), e($thread->board->category->color))) !!}
                    </blockquote>
                @endforeach
                @endif
                <br>
                {!! nl2br(Helper::bbcode_to_html(e($reply->body), e($reply->author->power), e($board->category->color))) !!}
            </blockquote>
            <form method="POST" id="post-form">
                @csrf
                <textarea id="body" name="body" placeholder="Body (max 1,000 characters)" style="width:100%;min-height:200px;font-size:16px;box-sizing:border-box;margin-top:10px;" required>{{ old('body') }}</textarea>
                <div class="center-text">
                    <button type="submit" class="button small-text {{ $board->category->color }}">
                        Create Quote
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
