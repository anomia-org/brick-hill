@extends('layouts.header')

@section('title', 'Outbox')

@section('content')

<div class="col-10-12 push-1-12">
    <div class="tabs">
        <a class="tab col-6-12" href="/messages">
            Inbox
        </a>
        <a class="tab active col-6-12" href="/messages/sent">
            Outbox
        </a>
        <div class="tab-holder" style="box-shadow:none;">
            <div class="tab-body active">
                <div class="content" style="padding:0px">
                    @foreach($messages as $message)
                    <a href="/message/{{ $message->id }}/">
                        <div class="hover-card thread-card m0">
                            <div class="col-7-12 topic">
                                <span class="small-text label dark">{{ $message->title }}</span><br>
                                <span class="label smaller-text" data-user-id="{{ $message->receiver->id }}">To <span style="color:#565656">{{ $message->receiver->username }}</span></span>
                            </div>
                            <div class="no-mobile overflow-auto topic">
                                <div class="col-1-1 stat" style="text-align:right;">
                                    <span class="title" title="{{ $message->updated_at }}">{{ Helper::time_elapsed_string($message->updated_at) }}</span><br>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                    @if(count($messages) == 0)
                    <div style="text-align:center;padding:10px;">
                        You don't have any messages!
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-10-12 push-1-12">
    <div class="pages">
        @if(isset($page['pages']))
            @foreach ($page['pages'] as $pageNum)
                <a class="page @if($page['current'] == $pageNum) blue weight600 @endif" href="/messages/sent/{{ $pageNum }}">{{ $pageNum }}</a>
            @endforeach
        @endif
    </div>
</div>
<script>
    $('span.label.smaller-text').on('click', (e) => {
        e.preventDefault();
        let userId = $(e.target).data('userId');
        if(typeof $(e.target).parent().data('userId') !== 'undefined')
            userId = $(e.target).parent().data('userId');
        window.location = `/user/${userId}/`
    })
</script>
@endsection