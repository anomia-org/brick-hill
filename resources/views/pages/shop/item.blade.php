@extends('layouts.header')

@section('title', $item->name)

@push('meta')
<meta property="og:title" content="{{ $item->name }}"/>
<meta property="og:image" content="{{ $item->thumbnail }}"/>
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="512">
<meta property="og:image:height" content="512">
<meta property="twitter:title" content="{{ $item->name }}"/>
<meta property="twitter:creator" content="@hill_of_bricks"/>
<meta property="twitter:image1" content="{{ $item->thumbnail }}"/>
<meta name="twitter:card" content="summary_large_image">
@endpush

@section('content')
<div class="col-10-12 push-1-12">
    <item-page 
        id="itempage-v"

        item-name="{{ $item->name }}"
        :item-id="{{ $item->id }}"
        sold="{{ $item->sold }}"
        :owns="{{ $owns ? "true" : "false" }}"

        @if($item->product)
        :product-id="{{ $item->product->id }}"
        @endif

        :is-official="{{ $is_official ? "true" : "false" }}"

        :has-buy-request="{{ $has_buy_requests }}"

        :serials="{{ $serials }}"
        
        on-load-favorites="{{ $favorite_count ?? 0 }}"
        :on-load-favorited="{{ $has_favorited ? "true" : "false" }}"
        :on-load-wishlisted="{{ $has_wishlisted ? "true" : "false" }}"
    />
</div>
@endsection