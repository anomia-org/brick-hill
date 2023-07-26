@extends('layouts.header')

@noAds

@section('title', 'Too Many Requests')

@section('content')
<div style="text-align:center;padding-top:50px;">
	<span style="font-weight:600;font-size:3rem;display:block;">Error 429: Too Many Requests</span>
	<span style="font-weight:500;font-size:2rem;display:block;padding-bottom:20px;">Stop mashing buttons :(</span>
</div>
@endsection