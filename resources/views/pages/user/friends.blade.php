@extends('layouts.header')

@section('title', 'Friends')

@section('content')
<div class="col-10-12 push-1-12">
    <div class="card">
        <div class="top blue">
            Friends
        </div>
        <div class="content text-center">
            @if(count($friends) == 0)
            <span>You don't have any friend requests</span>
            @endif
            <ul class="friends-list">
                @foreach($friends as $friend)
                <li class="col-1-5 mobile-col-1-1">
                    <div class="friend-card">
                        <a href="/user/{{ $friend->fromUser->id }}/">
                            <img src="{{ $friend->fromUser->avatar_thumbnail }}">
                            <div class="ellipsis">{{ $friend->fromUser->username }}</div>
                        </a>
                        <form method="POST" action="{{ route('friend') }}" class="accept">
                            @csrf
                            <input type="hidden" name="userId" value="{{ $friend->fromUser->id }}">
                            <input type="hidden" name="type" value="accept">
                        </form>
                        <form method="POST" action="{{ route('friend') }}" class="decline">
                            @csrf
                            <input type="hidden" name="userId" value="{{ $friend->fromUser->id }}">
                            <input type="hidden" name="type" value="decline">
                        </form>
                        <button class="button small green inline" style="left:10px;font-size:10px;" onclick="$('form.accept', $(this).parent()).submit()">ACCEPT</button>
                        <button class="button small red inline" style="right:10px;font-size:10px;" onclick="$('form.decline', $(this).parent()).submit()">DECLINE</button>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="pages">
        @if(isset($page['pages']))
            @foreach ($page['pages'] as $pageNum)
                <a class="page @if($page['current'] == $pageNum) active @endif" href="/friends/{{ $pageNum }}">{{ $pageNum }}</a>
            @endforeach
        @endif
    </div>
</div>
@endsection