@extends('layouts.header')

@section('title', 'New Trade')

@section('content')
<div class="new-theme">
    <p class="large-text bold" style="text-align: left; margin: 0;">Trade with {{ $user->username }}</p>
    <trade id="trade-v" receiver="{{ $user->id }}"  sender="{{ Auth::id() }}"></trade>
</div>
@endsection