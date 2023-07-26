@extends('layouts.header')

@section('title', 'Settings')

@section('content')
@if(request()->get('email_sent') && session('denied_email'))
<div class="col-10-12 push-1-12">
    <div class="alert error">
        You need to verify your email!
        <a href="{{ route('sendEmail') }}" class="button red forum-create-button" style="margin-right:15px;margin-left:10px;">Send Email</a>
    </div>
</div>
@endif
<div class="col-10-12 push-1-12">
    <settings id="settings-v"></settings>
</div>
@endsection
