@extends('layouts.header')

@section('title', 'Admin')

@section('content')
<div class="col-10-12 push-1-12" style="margin-bottom:5px;">
    Admin points: {{ number_format(auth()->user()->admin_points) }}
</div>
<div class="col-10-12 push-1-12">
    <admin-panel id="adminpanel-v"></admin-panel>
</div>
@endsection