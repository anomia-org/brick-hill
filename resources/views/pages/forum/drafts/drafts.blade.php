@extends('layouts.header')

@section('title', 'My Drafts')

@section('content')
@auth
<div class="col-10-12 push-1-12">
    @include('includes.forumbar', ['bookmarks' => $bookmarks])
</div>
@endauth
<div class="col-10-12 push-1-12">
    <div class="forum-bar weight600" style="padding:10px 5px 10px 0;">
        <a href="/forum/">Forum</a> <i class="fa fa-angle-double-right" style="font-size:1rem;" aria-hidden="true"></i> <a href="/forum/drafts/">My Drafts</a>
    </div>
    <div class="card">
        <div class="top blue">
            My Drafts
        </div>
        <div class="content" style="padding: 0px">
            @foreach($drafts as $draft)
            <div class="hover-card m0" style="height:64px;">
                <div class="p2 overflow-auto">
                    <div class="col-7-12 topic ellipsis">
                        <a href="/forum/draft/{{ $draft->id }}/"><span class="small-text">{{ $draft->title }}</span></a><br>
                        <span class="label smaller-text">By <a class="dark-gray-text" href="/user/{{ auth()->id() }}/">{{ auth()->user()->username }}</a> in <a class="dark-gray-text" href="/forum/{{$draft->board->id}}/"> {{$draft->board->name}} </a></span>
                    </div>
                    <div class="no-mobile overflow-auto topic">
                        <div class="col-6-12 stat pt2 center-text">
                            <span class="title">{{ Helper::time_elapsed_string($draft->updated_at) }}</span><br>
                        </div>
                        <div class="col-6-12 pt1 post ellipsis">
                            <form method="POST" action="{{ route('deleteDraft', $draft->id) }}">
                                @csrf
                                <button type="submit" class="push-right button small smaller-text {{ $draft->board->category->color }}">DELETE</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @if(count($drafts) == 0)
            <div class="center-text p2">
                Nothing here :(
            </div>
            @endif
        </div>
    </div>
    <div style="text-align:center;">
        @if(isset($pages['pages']))
            @foreach ($pages['pages'] as $pageNum)
                <a class="forumPage {{ ($pages['current'] == $pageNum) ? 'blue weight600' : ''}}" href="/forum/drafts/{{ $pageNum }}">{{ $pageNum }}</a>
            @endforeach
        @endif
    </div>
</div>
@endsection
