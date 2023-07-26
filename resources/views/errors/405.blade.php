@extends('layouts.header')

@noAds

@section('title', 'Method Not Allowed')

@section('content')
<div style="text-align:center;padding-top:50px;">
	<span style="font-weight:600;font-size:3rem;display:block;">Error 405: Method Not Allowed</span>
    <span style="font-weight:500;font-size:2rem;display:block;padding-bottom:20px;">Try going to another page</span>
</div>
@endsection