@extends('layouts.header')

@section('title', 'Friends')

@section('content')
<div class="col-10-12 push-1-12">
    <div class="card">
        <div class="top blue">
            Friends
        </div>
        <div class="content">
            @if(count($friends) == 0)
            <div style="text-align:center">
                <span>This user does not have any friends :(</span>
            </div>
            @endif
            <ul class="friends-list">
                @foreach($friends as $friend)
                @php
                // use to_id or from_id
                if($friend['to_id'] == request('id'))
                    $id = 'fromUser';
                else
                    $id = 'toUser';
                $friend = $friend[$id];
                @endphp
                <li class="col-1-5 mobile-col-1-1">
                    <a href="/user/{{ $friend->id }}/">
                        <div class="profile-card">
                            <img src="{{ $friend->avatar_thumbnail }}" style="height:150px;width:150px;">
                            <div class="ellipsis">{{ $friend->username }}</div>
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="pages">
        @if(isset($page['pages']))
            @foreach ($page['pages'] as $pageNum)
                <a class="page @if($page['current'] == $pageNum) active @endif" href="/user/{{ request('id') }}/friends/{{ $pageNum }}">{{ $pageNum }}</a>
            @endforeach
        @endif
    </div>
</div>
@endsection