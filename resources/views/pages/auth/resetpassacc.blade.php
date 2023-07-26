@extends('layouts.header')

@noAds

@section('title', 'Forgot Password')

@section('content')
<div class="col-10-12 push-1-12">
    <div class="card">
        <div class="top red">
            Accounts
        </div>
        <div class="content">
            @foreach($accounts as $user)
            <div style="height:153px;">
                <div class="col-2-12" style="display:inline-block;">
                    <img src="{{ $user->avatar_thumbnail }}" style="width:128px;">
                </div>
                <div class="col-10-12" style="padding-top:60px;">
                    <div style="min-width:200px;">
                        <b>{{ $user['username'] }}</b>
                    </div>
                    <form method="POST" action="{{ route('resetForgotPassword') }}">
                        @csrf
                        <input type="hidden" name="user" value="{{ $user['id'] }}">
                        <input type="hidden" name="key" value="{{ \Request::get('key') }}">
                        <input type="password" name="password" placeholder="Password" required>
                        <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
                        <div style="float:right;margin-top:-25px;">
                            <button class="blue" type="submit">RESET</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr>
            @endforeach
        </div>
    </div>
</div>
@endsection