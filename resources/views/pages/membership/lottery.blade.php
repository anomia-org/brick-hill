@extends('layouts.header')

@noAds

@section('title', 'Lottery')

@section('content')
<div class="col-10-12 push-1-12">
    <div class="card">
        <div class="top green">
            Membership Giveaway
        </div>
        <div class="content center-text">
            <div class="small-text">
                If you currently have an active membership, then you're automatically entered into this giveaway!
                The pool is determined by how many users have an active membership, and what membership type that is. At the end of each month, four random winners will be chosen from those with membership.
            </div>
            <hr>
            <div>
                <div class="large-text bucks-text very-bold">{{ number_format($pool) }} Bucks</div>
                <div class="small-text gray-text">Current pool size</div>
                <div class="small-text gray-text">
                    <b>{{ number_format(floor($pool/4)) }}</b> Bucks to be distributed to <b>4</b> winners.
                </div>
            </div>
            @if($previous_winners->count() > 0)
            <hr>
            <div class="lottery-winners">
                @foreach($previous_winners as $winner)
                <a href="/user/{{ $winner->user_id }}">
                    <div class="inline lottery-winner">
                        <img src="{{ $winner->user->avatar_thumbnail }}">
                        <div class="ellipsis">{{ $winner->user->username }}</div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
            <hr>
            <div class="medium-text very-bold">
                <countdown id="countdown-v" class="countdown" :reload="true" countdown-to="{{ $next_lottery->toISOString() }}"></countdown>
            </div>
            <div class="small-text darker-gray-text">until the winners are drawn</div>
            @if(!auth()->user()->membership()->exists())
            <a href="/membership">
                <button class="lottery-button white-text plain flat">
                    <div class="smedium-text">ENTER GIVEAWAY</div>
                </button>
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
