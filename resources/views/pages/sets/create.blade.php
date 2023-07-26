@extends('layouts.header')

@section('title', 'Create Set')

@section('content')
<div class="col-10-12 push-1-12">
    <div class="card">
        <div class="top blue">
            Create Set
        </div>
        <div class="content" style="position:relative">
            <form method="POST" action="{{ route('createSetPost') }}">
                @csrf
                @php
                if(substr(auth()->user()->username, -1) == 's') {
                    $title = auth()->user()->username."' Set";
                } else {
                    $title = auth()->user()->username."'s Set";
                }
                @endphp
                <input class="upload-input" type="text" name="name" placeholder="Title" required value="{{ (old('name')) ?? $title }}">
                <textarea class="upload-input" name="description" placeholder="Description" style="width:320px;height:100px;">{{ old('description') }}</textarea>
                <input class="button blue upload-submit" type="submit" value="Create">
            </form>
        </div>
    </div>
</div>
@endsection
