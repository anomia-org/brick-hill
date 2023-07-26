@extends('layouts.header')

@section('title', 'Dashboard')

@section('content')
@if(request()->has('paymentCompleted'))
<legacy-notification id="notification-v" class="notification" msg="Thank you for your purchase! Please allow 24 hours for the purchase to process." type="success"></legacy-notification>
@endif
@if(request()->has('clientCompleted'))
<legacy-notification id="notification-v" class="notification" msg="Thank you for purchasing client access! You can now download the installer on the downloads page." type="success"></legacy-notification>
@endif

<div class="new-theme dashboard">
    <div class="col-3-10">
        <div class="card border-bottom no-rounded no-shadow">
            <div class="rounded center-text">
                <img class="avatar-thumbnail" src="{{ auth()->user()->avatar_thumbnail }}" style="width:100%;">
                @if(!is_null(auth()->user()->primaryClan?->tag))
                    <a href="/clan/{{ auth()->user()->primary_clan_id }}"><span class="bold medium-text light-text">[{{ auth()->user()->primaryClan?->tag }}]</span></a>
                @endif
                <span style="margin: 5px;" class="bold medium-text">{{ auth()->user()->username }}</span>
                <div class="dashboard-info flex flex-column flex-items-center">
                    <div class="flex dash-info vmargin-6">
                        @if(auth()->user()->membership?->active)
                        <div class="flex streak-info ml-20">
                        @switch(auth()->user()->membership->membership)
                            @case(4)
                                <svg-sprite id="svgsprite-v" svg="user/dashboard/royal_streak_col" square="23" class="svgsprite mr-10"></svg-sprite>
                                @break
                            @case(3)
                                <svg-sprite id="svgsprite-v" svg="user/dashboard/ace_streak_col" square="23" class="svgsprite mr-10"></svg-sprite>
                                @break
                            @case(2)
                                <svg-sprite id="svgsprite-v" svg="user/dashboard/mint_streak_col" square="23" class="svgsprite mr-10"></svg-sprite>
                                @break
                            @default
                        @endswitch
                        <p class="smedium-text">{{ $streak }}</p>
                        </div>
                        @endif

                        <div class="flex streak-info dash-info ml-20 mr-10 flex-items-center">
                            <svg-sprite id="svgsprite-v" svg="user/trade/value/value" square="23" class="svgsprite svg-black mr-10"></svg-sprite>
                            @if($value = auth()->user()->tradeValues()->first())
                                <p class="smedium-text mr-20" title="{{ $value->value }}">{{ Helper::numAbbr($value->value) }}</p>
                                
                                @if($value->direction >= 0) 
                                <svg-sprite id="svgsprite-v" svg="user/trade/arrow_value_up" square="15" class="svgsprite ml-10 value-svg"></svg-sprite>
                                @else
                                <svg-sprite id="svgsprite-v" svg="user/trade/arrow_value_down" square="15" class="svgsprite ml-10 value-svg"></svg-sprite>
                                @endif
                            @else
                            <p class="smedium-text mr-20">{{ 0 }}</p>
                            @endif
                        </div>
                    </div>

                    @if(auth()->user()->isAdmin)
                    <span class="very-bold red-text administrator small-text mb-10">ADMINISTRATOR</span>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="flex flex-items-center flex-horiz-center">
                <p class="very-bold smedium-text mr-10">{{ $friend_count }}</p>
                <p class="bold small-text gray-text">{{ \Illuminate\Support\Str::plural('FRIEND', $friend_count) }}</p>
            </div>
        </div>
        
        <div class="card no-rounded no-shadow">
            @foreach($friends as $friend)
            @php
                if($friend['to_id'] == auth()->user()->id)
                    $id = 'fromUser';
                else
                    $id = 'toUser';
                $friend = $friend[$id];
            @endphp
            <div class="status">
                <a href="/user/{{ $friend->id }}">
                    <img src="{{ $friend->avatar_thumbnail }}">
                </a>
                <div class="ellipsis ml-5">
                    <div class="mb-12 smedium-text relative ellipsis flex">
                        <span href="/user/{{ $friend->id }}" class="ellipsis">{{ $friend->username }} </span>
                        <span class="status-dot status-dashboard @if($friend->is_online)online @endif"></span>
                    </div>
                    <a class="mb-12 bold small-text very-bold" href="/message/{{ $friend->id }}/send">MESSAGE</a>
                </div>
            </div>
            @endforeach
            @if($friend_count > 0)
            <a class="small-text bold" href="/user/{{ auth()->user()->id }}/friends/1">VIEW ALL</a>
            @endif
        </div>
    </div>
    <div class="col-8-12">
        <blog-card id="blogcard-v" class="blogcard"></blog-card>
        <div class="card border-bottom no-shadow no-rounded">
            <div class="smedium-text mb-16 bold">
                What's New?
            </div>
            <div>
                <form style="width:100%;" class="pb3" method="POST" action="{{ route('status') }}">
                    @csrf
                    <div class="flex flex-column">
                        <textarea class="post-input border mb-16" name="status" placeholder="Right now I'm..." type="text"></textarea>
                        <button class="post-button button small smaller-text blue">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card no-shadow">
            <div class="smedium-text mb-16 bold">
                Your Feed
            </div>
            <div>
                @if(count($feed) == 0)
                    <p class="gray-text">Your feed is empty! Follow some users to fill this area.</p>
                @endif

                @foreach($feed as $status)
                @if(!empty($status->body))
                <div class="status border-bottom mb-12 flex">
                    @if($status['type'] == 'clan')
                    <a href="/clan/{{ $status->clan_id }}">
                        <img src="{{ $status['clan']->thumbnail }}">
                    </a>
                    @else
                    <a href="/user/{{ $status->owner_id }}">
                        <img src="{{ $status->user->avatar_thumbnail }}">
                    </a>
                    @endif
                    <div class="ml-5 mb-10">
                        <div class="flex">
                            @if($status['type'] == 'clan')
                            <a href="/clan/{{ $status->clan_id }}" class="very-bold mr-5">{{ $status->clan['title'] }} </a>
                            @else
                            <a href="/user/{{ $status->owner_id }}" class="very-bold mr-5">{{ $status->user['username'] }} </a>
                            @endif
                            <span class="mr-5">&#183;</span>
                            <span class="dark-gray-text mb-12 small-text" title="{{ Carbon\Carbon::parse($status->date)->format('d/m/Y h:i A') }}">{{ Carbon\Carbon::parse($status->date)->diffForHumans() }}</span>
                        </div>
                        <div>{{ $status->body }}</div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
