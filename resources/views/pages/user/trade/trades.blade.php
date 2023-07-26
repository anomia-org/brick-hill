@extends('layouts.header')

@section('title', 'Trades')

@section('content')
<view-trades id="viewtrades-v" user="{{ auth()->id() }}"></view-trades>
@endsection
