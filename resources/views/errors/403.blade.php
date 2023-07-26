@extends('layouts.header')

@noAds

@section('title', 'Not Authorized')

@section('content')
<div style="text-align:center;padding-top:50px;">
    <span style="font-weight:600;font-size:3rem;display:block;">Error 403: Not Authorized</span>
    <span style="font-weight:500;font-size:2rem;display:block;padding-bottom:20px;">You do not have the necessary access to view this page</span>
</div>
@endsection