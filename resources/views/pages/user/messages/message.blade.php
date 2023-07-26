@extends('layouts.header')

@section('title', $message->title)

@section('content')
<div class="col-10-12 push-1-12">
    <div class="card">
        <div class="top blue">
            {{ $message->title }}
        </div>
        <div class="content" style="position:relative;">
            <div class="user-info" style="width:250px;overflow:hidden;display:inline-block;float:left;">
                <a href="/user/{{ $message->sender->id }}/">
                    <img src="{{ $message->sender->avatar_thumbnail }}" style="width:200px;display:block;">
                    <span style="white-space:nowrap;">{{ $message->sender->username }}</span>
                </a>
            </div>
            <div style="padding-left:250px;padding-bottom:10px;">
                @if($message->author_id == config('site.main_account_id') && $message->message == '[bhstartmsg]')
                Hello there, {{ Auth::user()->username }}!
                <br><br>
                Welcome to Brick Hill, we're glad to have you here. We hope you have a wonderful time, but to make sure you can make the most here, we ask that you brush up on a few of our guidelines.
                <br><br>
                <a style="color:#444" href="/rules">Here</a> are some <a style="color:#444" href="/forum/thread/1/">basic rules</a> to get you settled in with the crowd! If you're unsure about anything then you can always take a look at our <a style="color:#444" href="/terms">Terms of Service</a>, which will always be situated at the footer of each page!
                <br><br>
                Thanks for stopping by,<br>
                Brick Hill
                @else
                {!! nl2br(e($message->message)) !!}
                @endif
            </div>
            <div class="admin-forum-options" style="position:absolute;bottom:0;right:2px;padding-bottom:5px;">
                @can('scrub users')
                <form method="POST" action="/message/{{ $message->id }}/scrub">
                    @csrf
                    <button type="submit" name="type" value="scrub">Scrub</button>
                </form>
                @endcan
                @can('ban')
                <a href="/user/{{ $message->sender->id }}}/ban/message/{{ $message->id }}" class="dark-gray-text cap-text">Ban</a>
                @endcan
                <a href="/report/message/{{ $message->id }}" class="dark-gray-text cap-text">Report</a>
            </div>
        </div>
    </div>
    @if(auth()->id() == $message['recipient_id'])
    <div class="card reply-card" style="display:none;">
        <div class="content" style="padding:15px;">
            <form method="POST" action="{{ route('message') }}">
                @csrf
                <input type="hidden" name="msgId" value="{{ $message->id }}">
                <input type="hidden" name="title" value="{{ $message->title }}">
                <textarea name="reply" style="width:100%;height:250px;box-sizing:border-box;">{{ old('reply') }}</textarea>
                <button class="forum-button blue" style="margin: 10px auto 10px auto;display:block;" type="submit">SEND</button>
            </form>
        </div>
    </div>
    <div class="center-text">
        <a class="button blue inline" style="margin: 10px auto 10px auto;">REPLY</a>
    </div>
    <script>
        $('.button.blue.inline').on('click touchstart', () => {
            $('.card.reply-card').css('display', 'block');
            $('.button.blue.inline').remove();
        })
    </script>
    @endif
    
</div>
@endsection