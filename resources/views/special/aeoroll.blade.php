@extends('layouts.header')

@section('title', 'Maintenance')

@section('content')
@if(!Auth::check())
    <div class="alert error">
        You can't win the aeo roll unless you login
    </div>
@endif
@if($won)
    <div class="alert success">
        You won the aeo roll!! Your account has been granted the Jackpot award.
    </div>
@endif
<div style="text-align:center;padding-top:50px;">
    <span style="font-weight:600;font-size:3rem;display:block;">Welcome to the new Brick Hill</span>
    <span style="font-weight:500;font-size:2rem;">We are currently under maintenance.</span>
    <div style="text-align:center;margin:20px;">
        @foreach($rands as $rand)
        <img style="width:20%;" src="{{ config('site.storage.domain') }}/images/avatars/{{ $rand }}.png">
        @endforeach
    </div>
    <form style="margin-top:20px;">
        <input type="password" name="maint_key" placeholder="Maintenance Key">
        <div style="padding:2.5px;"></div>
        <button type="submit" class="blue">SUBMIT</button>
    </form>
</div>
@endsection