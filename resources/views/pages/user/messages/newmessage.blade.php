@extends('layouts.header')

@section('title', 'Create Message')

@section('content')
<div class="col-10-12 push-1-12">
    <div class="card">
        <div class="top blue">
            Send Message to {{ $user['username'] }}
        </div>
        <div class="content" style="padding:15px;">
            <form method="POST" action="{{ route('message') }}">
                @csrf
                <input name="title" placeholder="Title" style="width:100%;margin-bottom:10px;box-sizing:border-box;">
                <input type="hidden" name="recipient" value="{{ $user->id }}">
                <textarea name="reply" style="width:100%;height:250px;box-sizing:border-box;" placeholder="Hey {{ $user->username }}">{{ old('reply') }}</textarea>
                <button class="forum-button blue" style="margin: 10px auto 10px auto;display:block;" type="submit">SEND</button>
            </form>
        </div>
    </div>
</div>
@endsection