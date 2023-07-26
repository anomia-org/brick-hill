@extends('layouts.header')

@section('title', 'Clans')

@section('content')
<div class="col-10-12 push-1-12">
    <div class="card">
        <div class="top orange">
            Clans
        </div>
        <div class="content">
            @if(count($clans) > 0)
            @foreach ($clans as $clan)
            <a href="/clan/{{ $clan['clan']['id'] }}/">
                <div class="profile-card not-padded" style="width:calc(100% / 6 - 8px)">
                    <img src="{{ $clan['clan']->thumbnail }}">
                    <span class="ellipsis">{{ $clan['clan']['title'] }}</span>
                </div>
            </a>
            @endforeach
            @else
            <div style="text-align:center">
                <span>This user is not in any clans</span>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection