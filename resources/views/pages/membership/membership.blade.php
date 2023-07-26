@extends('layouts.header')

@section('title', 'Membership')

@section('content')
<membership id="membership-v" :already-has-membership="{{ auth()->user()->membership()->exists() ? "true" : "false" }}"></membership>
@endsection
