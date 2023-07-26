@extends('layouts.header')

@section('title', 'Staff')

@section('content')
<div class="col-10-12 push-1-12 new-theme">
    <div>
        <div class="content">
            <div class="header">Staff</div>
            <div class="col-1-1" style="padding-right:0;">
                @foreach ($users as $user)
                    <a href="/user/{{ $user['id'] }}" class="mb-10">
                        <div class="search-user-card flex">
                            <img src="{{ $user->avatar_thumbnail }}">
                            <div class="width-100 data ellipsis" style="margin-left:10px;height:100%">
                                <div>
                                    <b>{{ $user['username'] }}</b>
                                    <span style="float:right;" class="status-dot @if($user->is_online)online @endif"></span>
                                </div>
                                <p class="data mt-2 description">{!! nl2br(e($user['description'])) !!}</p>
                            </div>
                        </div>
                        <div class="divider mb-20"></div>
                    </a>
                @endforeach
            </div>
            <div class="pages">
                @if(isset($pages['pages']))
                    @foreach ($pages['pages'] as $pageNum)
                        <a class="page {{ ($pages['current'] == $pageNum) ? 'active' : ''}}" href="/staff/{{ $pageNum }}">{{ $pageNum }}</a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
