@extends('layouts.header')

@noAds

@section('title', 'Internal Server Error')

@section('content')
<div style="text-align:center;padding-top:50px;">
    <span style="font-weight:600;font-size:3rem;display:block;">Error 500: Internal Server Error</span>
    <span style="font-weight:500;font-size:2rem;display:block;padding-bottom:20px;">Looks like something went wrong :(</span>
    <span style="font-weight:500;font-size:1.2rem;">If this keeps happening email us at <a href="mailto:help@brick-hill.com" style="color:#444;">help@brick-hill.com</a></span>
</div>
@endsection