@extends('layouts.header')

@section('title', 'Awards')

@section('content')
<div class="col-10-12 push-1-12">
    @foreach($categories as $name => $category)
    <div class="card">
        <div class="top {{ $colors[$name] }}">
            {{ $name }}
        </div>
        <div class="content">
            @foreach($category as $award)
            <div class="award-card">
                <img src="{{ asset("images/awards/" . $award['id'] . ".png") }}">
                <div class="data">
                    <div class="very-bold">{{ $award['name'] }}</div>
                    <div style="padding:1px;"></div>
                    <span>{{ $award['description'] }}</span>
                </div>
            </div>
            @if(!$loop->last)
            <hr>
            @endif
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection