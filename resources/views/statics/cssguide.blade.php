@extends('layouts.header')

@section('title', 'CSS Guide')

@section('content')
<!-- All pages using new css must have a root div with new-theme class -->
<div class="col-10-12 push-1-12 new-theme">
    <div id="buttons">
        <div class="header">BUTTONS</div>

        <button class="yellow">Example yellow</button>
        <button class="blue">Example blue</button>
        <button class="green">Example green</button>
        <button class="clear">Example clear</button>
    </div>
</div>
@endsection