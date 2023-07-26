@extends('layouts.header')

@section('title', 'Sets')

@section('content')
<div class="col-10-12 push-1-12">
    <div class="large-text mb1">
        My Sets
        @if(count($sets) > 0)
        <a class="button blue push-right small-text" href="{{ route('createSet') }}">CREATE SET</a>
        @endif
    </div>
    @if(count($sets) == 0)
    <div class="center-text">
        <div class="medium-text mb2">You don't have any sets</div>
        <a class="button blue" href="{{ route('createSet') }}">CREATE SET</a>
    </div>
    @else
    @foreach($sets as $set)
    <div class="col-1-4 mobile-col-1-3 set">
        <div class="card ellipsis">
            <div class="thumbnail no-padding">
                <a href="/play/{{ $set->id }}/">
                    <img class="round-top" src="{{ config('site.storage.domain') }}{{ $set->thumbnail }}">
                </a>
            </div>
            <div class="content">
                <div class="name game-name ellipsis">
                    <a href="/play/{{ $set->id }}/">{{ $set->name }}</a>
                </div>
                <div class="creator ellipsis">
                    By <a href="/user/{{ $set->creator_id }}/">{{ auth()->user()->username }}</a>
                </div>
            </div>
            <div class="footer">
                <div class="playing">{{ $set->playing }} Playing</div>
            </div>
        </div>
    </div>
    @endforeach
    @endif
</div>
@endsection
