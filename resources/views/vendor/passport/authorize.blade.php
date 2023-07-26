@extends('layouts.header')

@section('title', 'Authorize')

@section('content')
<div class="col-6-12 push-3-12">
    <div class="card">
        <div class="top blue">
            Authorization Request
        </div>
        <div class="content">
            <div><b>{{ $client->name }}</b> is requesting permission to access your account.</div>
            @if (count($scopes) > 0)
                <div style="margin-top:10px;">
                    <div><b>This application will be able to:</b></div>
                    <ul class="normal" style="margin: 5px 0;">
                        @foreach ($scopes as $scope)
                            <li class="normal">{{ $scope->description }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="center-text">
                <form class="inline" method="post" action="{{ route('passport.authorizations.approve') }}">
                    @csrf
                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit" class="button green">Authorize</button>
                </form>
                <form class="inline" method="post" action="{{ route('passport.authorizations.deny') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button class="button red">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
