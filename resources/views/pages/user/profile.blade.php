@extends('layouts.header')

@section('title', $user->username)

@section('content')
<div class="col-10-12 push-1-12">
    @if($banned)
    <div class="alert error">
        User is banned
    </div>
    @endif
    @if($status)
    <div class="col-1-1" style="padding-right:0;">
        <div class="card">
            <div class="content" style="border-radius:5px;position:relative;word-break:break-word">
                <div class="small-text very-bold light-gray-text">What's on my mind:</div>
                @auth
                    @can('update', $user)
                    @can('scrub users')
                    <form method="POST" action="/user/{{ $user->id }}/scrubStatus">
                        @csrf
                        <a onclick="$(this).parent().submit()" class="dark-gray-text" style="position:absolute;top:5px;right:5px;font-size:13px;">Scrub</a>
                    </form>
                    @endcan
                    @endcan
                @endauth
                {{ $status['body'] }}
            </div>
        </div>
    </div>
    @endif
    <div class="col-6-12">
        <div class="card">
            <div class="content text-center bold medium-text relative ellipsis">
                <span class="status-dot @if($user->is_online)online @endif"></span>
                @if(!is_null($user->primaryClan?->tag))
                    <a href="/clan/{{ $user->primary_clan_id }}"><span class="mr1" style="color:#999999;">[{{ $user->primaryClan?->tag }}]</span></a>
                @endif
                <span class="ellipsis">{{ $user->username }}</span>
                <br>
                <img src="{{ $user->avatar_thumbnail }}" style="height:350px;">
                <div class="user-description-box closed">
                    <div class="toggle-user-desc gray-text">
                        <div class="user-desc p2 darker-grey-text" style="font-size:16px;line-height:17px;">
                            {!! nl2br(e($user->description)) !!}
                            @if($user->pastUsernames->count() > 0)
                            <p><hr></p>
                            Previously known as: <i>{{ $user->pastUsernames->pluck('old_username')->implode(', ') }}</i>
                            @endif
                        </div>
                        <a class="darker-grey-text read-more-desc" style="font-size:16px;">Read More</a>
                    </div>
                </div>
                @auth
                <profile-dropdown id="profiledropdown-v" 
                    :for_user="{{ $user->id }}" 
                    @can('view', $user) :can_view="true" @endcan
                    @can('update', $user) :can_update="true" @endcan
                ></profile-dropdown>
                @if($user->id !== auth()->id())
                <div style="text-align:center;">
                    <a class="button small blue inline" style="font-size:14px;" href="{{ route('sendMessage', ['id' => $user->id]) }}">MESSAGE</a>
                    <a class="button small blue inline" href="{{ route('sendTrade', ['id' => $user->id]) }}" style="font-size:14px;">TRADE</a>
                    <a class="button small inline {{ $friended[1] }}" style="font-size:14px;" onclick="$('.friend-form').submit();">{{ $friended[2] }}</a>
                    <form method="POST" action="{{ route('friend') }}" class="friend-form" class="inline">
                        @csrf
                        <input type="hidden" name="userId" value="{{ $user->id }}">
                        <input type="hidden" name="sender" value="profile">
                        <input type="hidden" name="type" value="{{ $friended[0] }}">
                    </form>
                </div>
                @endif
                @endauth
            </div>
        </div>
        @if(count($awards) > 0)
        <div class="card">
            <div class="top green">
                Awards
            </div>
            <div class="content" style="text-align:center;">
                @foreach($awards as $award)
                <a href="/awards/">
                    <div class="profile-card award">
                        <img src="{{ asset("images/awards/" . $award['award']['id'] . ".png") }}">
                        <span class="ellipsis">{{ $award['award']['name'] }}</span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    <div class="col-6-12" style="padding-right:0;">
        <sets id="sets-v" user_id="{{ $user->id }}" class="set-slider" style="position: relative"></sets>
        @ads
        <div id="100128-19">
            <script src="//ads.themoneytizer.com/s/gen.js?type=19"></script>
            <script src="//ads.themoneytizer.com/s/requestform.js?siteId=100128&formatId=19"></script>
        </div>
        @endads
    </div>
    <div class="col-1-1 tab-buttons">
        <button class="tab-button blue" data-tab="1">CRATE</button>
        <button class="tab-button transparent" data-tab="2">SOCIAL</button>
        <button class="tab-button transparent" data-tab="3">STATS</button>
    </div>
    <div class="col-1-1" id="tabs">
        <div class="button-tabs">
            <div class="button-tab active" data-tab="1">
                <div class="col-1-1">
                    <div class="card">
                        <div class="top red">
                            Crate
                        </div>
                        <div class="content">
                            <crate id="crate-v" user="{{ $user->id }}"></crate>
                        </div>
                    </div>
                </div>
            </div>
            <div class="button-tab" data-tab="2">
                <div class="row" style="padding-right:0.1px;">
                    <div class="col-6-12">
                        <div class="card">
                            <div class="top orange" style="position:relative;">
                                Clans
                                <a class="button orange" href="/user/{{ $user->id }}/clans" style="position:absolute;right:5px;top:4px;padding:5px;">SEE ALL</a>
                            </div>
                            <div class="content" style="text-align:center;min-height:330.86px;">
                                @if(count($clans) > 0)
                                @foreach ($clans as $clan)
                                <a class="col-1-3" href="/clan/{{ $clan->clan->id }}/" style="padding-right:5px;padding-left:5px;">
                                    <div class="profile-card">
                                        <img src="{{ $clan->clan->thumbnail }}">
                                        <span class="ellipsis">{{ $clan->clan->title }}</span>
                                    </div>
                                </a>
                                @endforeach

                                @else
                                <div class="center-text">
                                    <span>This user is not in any clans</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-6-12">
                        <div class="card">
                            <div class="top red" style="position:relative;">
                                Friends
                                <a class="button red" href="/user/{{ $user->id }}/friends/1" style="position:absolute;right:5px;top:4px;padding:5px;">SEE ALL</a>
                            </div>
                            <div class="content" style="text-align:center;min-height:330.86px;">
                                @if(count($friends) > 0)
                                @foreach ($friends as $friend)
                                @php
                                // use to_id or from_id
                                if($friend['to_id'] == $user->id)
                                    $id = 'fromUser';
                                else
                                    $id = 'toUser';
                                $friend = $friend[$id];
                                @endphp
                                <a class="col-1-3" href="/user/{{ $friend->id }}/" style="padding-right:5px;padding-left:5px;">
                                    <div class="profile-card user">
                                        <img src="{{ $friend->avatar_thumbnail }}">
                                        <span class="ellipsis">{{ $friend->username }}</span>
                                    </div>
                                </a>
                                @endforeach
                                @else
                                <span>This user does not have any friends :(</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="button-tab" data-tab="3">
                <div class="col-1-1">
                    <div class="card">
                        <div class="top red">
                            Statistics
                        </div>
                        <div class="content" style="min-height:330.86px;">
                            <table class="stats-table">
                                @php
                                $statsTable = [
                                    'Join Date' => Carbon\Carbon::parse($user->created_at)->format('d/m/Y'),
                                    'Last Online' => Helper::time_elapsed_string($user->last_online, true),
                                    'Game Visits' => number_format($totalVisits),
                                    'Forum Posts' => number_format($user->post_count),
                                    'Friends' => number_format($user->friends()->isAccepted()->count())
                                ];
                                @endphp
                                @foreach ($statsTable as $stat => $val)
                                <tr>
                                    <td>
                                        <b>{{ title_case($stat) }}:</b>
                                    </td>
                                    <td id="{{ str_replace(' ', '-', strtolower($stat)) }}">
                                        {{ $val }}
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    if($('.user-description-box .user-desc').height() <= 80) {
        $('.read-more-desc').css('display', 'none');
        $('.toggle-user-desc').addClass('open');
    }
    $(document).on('click', '.read-more-desc', function () {
        $(this).parent().parent().toggleClass('closed');
        if($(this).text() == 'Read More') {
            $(this).text('Show Less');
            $('.user-description-box .content').css('min-height', $('.user-description-box .content').height() + 33)
        } else {
            $(this).text('Read More');
            $('.user-description-box .content').css('min-height', $('.user-description-box .content').height() - 33)
        }
    })
</script>
@endsection
