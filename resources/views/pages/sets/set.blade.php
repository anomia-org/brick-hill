@extends('layouts.header')

@section('title', $set->name)

@section('content')
<div class="col-10-12 push-1-12">
    <set-page
        id="setpage-v"
        :set-id="{{ $set->id }}"
        set-name="{{ $set->name }}"
        set-thumbnail="{{ config('site.storage.domain') . $set->thumbnail }}"

        @if(!$set->is_dedicated)
        set-ip="{{ ($server && !\Carbon\Carbon::parse($server->last_post)->addMinutes(6)->isPast()) ? strrev(base64_encode($server->ip_address ?? "")) : "" }}"
        set-port="{{ $server->port ?? "" }}"
        set-playable="{{ $server && !\Carbon\Carbon::parse($server->last_post)->addMinutes(6)->isPast() }}"
        @else
        set-ip="{{ strrev(base64_encode($master_server->ip ?? "")) }}"
        set-port="42480"
        set-playable="1"
        @endif

        on-load-favorites="{{ $favorite_count ?? 0 }}"
        :on-load-favorited="{{ $has_favorited ? "true" : "false" }}"

        @auth
            :on-load-rated="{{ $rating !== null ? "true" : "false" }}"
            on-load-rating="{{ $rating?->is_liked ?? 0 }}"
        @endauth
        on-load-down-ratings="{{ $down_ratings ?? 0 }}"
        on-load-up-ratings="{{ $up_ratings ?? 0 }}"
    ></set-page>
</div>
@endsection