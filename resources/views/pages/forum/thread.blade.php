@extends('layouts.header')

@section('title', $thread['title'])

@section('content')
@if($thread->deleted)
<div class="col-10-12 push-1-12">
    <div class="alert error">
        This thread has been deleted and can only be viewed by administrators.
    </div>
</div>
@endif
@if($thread->hidden)
@can('manage forum')
<div class="col-10-12 push-1-12">
    <div class="alert warning">
        This thread has been hidden and can only be viewed by administrators or the creator of the thread.
    </div>
</div>
@endcan
@endif
@auth
<div class="col-10-12 push-1-12">
    @include('includes.forumbar', ['bookmarks' => $bookmarks])
</div>
@endauth
<div class="col-10-12 push-1-12">
    <div class="forum-bar mb2 ellipsis">
        <div class="inline mt2">
        <a href="/forum/">Forum</a>
        <i class="fa fa-angle-double-right" aria-hidden="true"></i>
        <a href="/forum/{{ $thread->board->id }}/">
            {{ $thread->board->name }}
        </a>
        <i class="fa fa-angle-double-right" aria-hidden="true"></i>
        <a href="/forum/thread/{{ $thread->id }}/">
            <span class="very-bold">
                {{ $thread->title }}
            </span>
        </a>
        </div>
        @auth
        <div class="push-right">
            <a class="button small {{ $thread->board->category->color }}" href="/forum/{{ $thread->board->id }}/create/">CREATE</a>
        </div>
        @endauth
    </div>
    <div class="card">
        <div class="top {{ $thread->board->category->color }}">
            @if($thread->pinned)
            <span class="thread-label {{ $thread->board->category->color }}">Pinned</span>
            @endif
            @if($thread->locked)
            <span class="thread-label {{ $thread->board->category->color }}">Locked</span>
            @endif
            {{ $thread->title }}
            @auth
            <div style="float:right">
            <form method="POST" action="{{ route('bookmark', $thread->id) }}" id="bookmark-submit">
                @csrf
                <label class="hover-cursor" for="bookmarkSubmit"><i class="{{ ($bookmarked) ? 'fas' : 'far' }} fa-bookmark"></i></label>
                <input type="submit" id="bookmarkSubmit" style="display:none;">
            </form>
            </div>
            @endauth
        </div>
        <div class="content">
            @if($pages['current'] <= 1)
            <div class="thread-row" style="position:relative;">
                <div class="overflow-auto">
                    <div class="col-3-12 center-text ellipsis">
                        <a href="/user/{{ $thread->author->id }}/">
                            <img src="{{ $thread->author->avatar_thumbnail }}" style="width:150px;">
                        </a>
                        <br>
                        @if(!is_null($thread->author->primaryClan?->tag))
                        <a href="/clan/{{ $thread->author->primary_clan_id }}/" class="light-gray-text">
                            {{ '['.$thread->author->primaryClan->tag.'] ' }}
                        </a>
                        @endif
                        <a href="/user/{{ $thread->author->id }}/">
                            {{ $thread->author->username }}
                        </a>
                        <br>
                        <span class="light-gray-text">
                            Joined {{ Carbon\Carbon::parse($thread->author->created_at)->format('d/m/Y') }}
                        </span>
                        <br>
                        <span class="light-gray-text">
                            Posts {{ number_format($thread->author->post_count) }}
                        </span>
                        @if($thread->author->power > \App\Constants\AdminPower::FORUM_TAG_ADMIN)
                        <div class="red-text"><i class="fas fa-gavel"></i>Administrator</div>
                        @elseif($thread->author->power > \App\Constants\AdminPower::FORUM_TAG_MOD)
                        <div style="color:#0f0fa2;"><i class="fas fa-gavel"></i>Moderator</div>
                        @endif
                    </div>
                    <div class="col-9-12">
                        <div class="weight600 light-gray-text text-right mobile-center-text" style="text-align:right;">
                            {{ \Carbon\Carbon::parse($thread->created_at)->format('h:i A d/m/Y') }}
                        </div>
                        <div class="p">
                            {!! nl2br(Helper::bbcode_to_html(e($thread->body), e($thread->author->power), e($thread->board->category->color))) !!}
                        </div>
                    </div>
                </div>
                @auth
                @can('manage forum')
                <div class="col-1-2 weight600 forum-options admin-forum-options" data-post-id="{{ $thread->id }}">
                    <form method="POST" action="/forum/thread/{{ $thread->id }}/scrub">
                        @csrf
                        <input type="hidden" name="toggle" value="{{ (int)!$thread->scrubbed }}">
                        <button class="report" type="submit">@if($thread->scrubbed)UNSCRUB @else SCRUB @endif</button>
                    </form>
                    <form method="POST"  action="/forum/thread/{{ $thread->id }}/delete">
                        @csrf
                        <input type="hidden" name="toggle" value="{{ (int)!$thread->deleted }}">
                        <button class="report" type="submit">@if($thread->deleted)UNDELETE @else DELETE @endif</button>
                    </form>
                    <a class="report" href="/user/{{ $thread->author->id }}/ban/forumthread/{{ $thread->id }}">BAN</a>
                    <button class="report" id="move-thread">MOVE</button>
                    <form method="POST" action="/forum/thread/{{ $thread->id }}/lock">
                        @csrf
                        <input type="hidden" name="toggle" value="{{ (int)!$thread->locked }}">
                        <button class="forum-reply" type="submit">@if($thread->locked)UNLOCK @else LOCK @endif</button>
                    </form>
                    <form method="POST" action="/forum/thread/{{ $thread->id }}/pin">
                        @csrf
                        <input type="hidden" name="toggle" value="{{ (int)!$thread->pinned }}">
                        <button class="forum-quote" type="submit">@if($thread->pinned)UNPIN @else PIN @endif</button>
                    </form>
                    <form method="POST" action="/forum/thread/{{ $thread->id }}/hide">
                        @csrf
                        <input type="hidden" name="toggle" value="{{ (int)!$thread->hidden }}">
                        <button class="forum-quote" type="submit">@if($thread->hidden)UNHIDE @else HIDE @endif</button>
                    </form>
                </div>
                @endcan
                <div
                    class="@can('manage forum') col-1-2 @else col-1-1 @endcan weight600 dark-grey-text forum-options"
                    style="text-align:right;"
                    data-post-id="{{ $thread->id }}"
                >
                    <a class="forum-reply mr4" href="/forum/reply/{{ $thread->id }}/">REPLY</a>
                    <a class="report" href="/report/forumthread/{{ $thread->id }}/">REPORT</a>
                </div>
                @endauth
            </div>
            <hr>
            @endif
            @if(count($replies) > 0)
                @foreach($replies as $reply)
                <div class="thread-row" style="position:relative;" id="post{{ $reply->id }}">
                    <div class="overflow-auto">
                        <div class="col-3-12 center-text ellipsis">
                            <a href="/user/{{ $reply->author->id }}/">
                                <img src="{{ $reply->author->avatar_thumbnail }}" style="width:150px;">
                            </a>
                            <br>
                            @if(!is_null($reply->author->primaryClan?->tag))
                            <a href="/clan/{{ $reply->author->primary_clan_id }}/" class="light-gray-text">
                                {{ '['.$reply->author->primaryClan->tag.'] ' }}
                            </a>
                            @endif
                            <a href="/user/{{ $reply->author->id }}/">
                                {{ $reply->author->username }}
                            </a>
                            <br>
                            <span class="light-gray-text">
                                Joined {{ Carbon\Carbon::parse($reply->author->created_at)->format('d/m/Y') }}
                            </span>
                            <br>
                            <span class="light-gray-text">
                                Posts {{ number_format($reply->author->post_count) }}
                            </span>
                            @if($reply->author->power > \App\Constants\AdminPower::FORUM_TAG_ADMIN)
                            <div class="red-text"><i class="fas fa-gavel"></i>Administrator</div>
                            @elseif($reply->author->power > \App\Constants\AdminPower::FORUM_TAG_MOD)
                            <div style='color:#0f0fa2;'><i class="fas fa-gavel"></i>Moderator</div>
                            @endif
                        </div>
                        <div class="col-9-12">
                            <div class="weight600 light-gray-text mobile-center-text" style="text-align:right;">
                                {{ Carbon\Carbon::parse($reply->created_at)->format('h:i A d/m/Y') }}
                            </div>
                            <div class="p">
                                @if($reply->quote_id && count($reply->quotes) > 0)
                                @foreach($reply->quotes as $quote)
                                    <blockquote class="{{ $thread->board->category->color }}">
                                        <em>Quote from
                                            <a href="https://www.brick-hill.com/user/{{ $quote->author->id }}">
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
                                {!! nl2br(Helper::bbcode_to_html(e($reply->body), e($reply->author->power), e($thread->board->category->color))) !!}
                            </div>
                        </div>
                    </div>
                    @auth
                    @can('manage forum')
                    <div class="col-1-2 weight600 forum-options admin-forum-options" data-post-id="{{ $reply->id }}">
                        <form method="POST" action="/forum/post/{{ $reply->id }}/scrub">
                            @csrf
                            <input type="hidden" name="toggle" value="{{ (int)!$reply->scrubbed }}">
                            <button class="report" type="submit" name="type" value="scrub">@if($reply->scrubbed)UNSCRUB @else SCRUB @endif</button>
                        </form>
                        <a class="report" href="/user/{{ $reply->author->id }}/ban/forumpost/{{ $reply->id }}">BAN</a>
                    </div>
                    @endcan
                    <div class="@can('manage forum') col-1-2 @else col-1-1 @endcan weight600 dark-grey-text forum-options" style="text-align:right;" data-post-id="{{ $reply['id'] }}">
                        <a class="forum-quote mr4" href="/forum/reply/{{ $thread->id }}/quote/{{ $reply->id }}/">QUOTE</a>
                        <a class="forum-reply mr4" href="/forum/reply/{{ $thread->id }}/">REPLY</a>
                        <a class="report" href="/report/forumpost/{{ $reply->id }}/">REPORT</a>
                    </div>
                    @endauth
                </div>
                <hr>
                @endforeach
                @endif
            </div>
        </div>
        <div class="center-text">
            @if(isset($pages['pages']))
                <div class="pages mb2">
                @foreach ($pages['pages'] as $pageNum)
                    <a class="page {{ ($pages['current'] == $pageNum) ? 'active' : ''}}" href="/forum/thread/{{ $thread->id }}/{{ $pageNum }}">{{ $pageNum }}</a>
                @endforeach
                </div>
            @endif
            @auth
            @if($thread->locked)
            <a class="button no-click">REPLY</a>
            @else
            <a class="button {{ $thread->board->category->color }}" href="/forum/reply/{{ $thread->id }}/">REPLY</a>
            @endif
            @endauth
        </div>
        @if($pages['current'] <= 1)
        @can('manage forum')
        <dropdown id="dropdown-v" class="dropdown" activator="move-thread">
            <ul>
                <form method="POST" action="/forum/thread/{{ $thread->id }}/move" style="width:100%;padding:0;" id="move-form">
                    <li style="background-color:#fff;border-radius:5px;">
                        @csrf
                        <select name="location">
                            @foreach($boards as $board)
                                <option value="{{ $board->id }}" @if($board->id == $thread->board->id)selected @endif>{{ $board->name }}</option>
                            @endforeach
                        </select>
                    </li>
                    <li style="background-color:#fff;border-radius:5px;">
                        <a>Warn? <input style="display: inline-block;" type="checkbox" name="warn"></a>
                    </li>
                </form>
                <li style="background-color:#fff;border-radius:5px;">
                    <a onclick="$('#move-form').submit();">MOVE</a>
                </li>
            </ul>
        </dropdown>
        @endcan
        @endif
</div>
@endsection
