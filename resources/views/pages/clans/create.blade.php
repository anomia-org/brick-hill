@extends('layouts.header')

@section('title', 'Create Clan')

@section('content')
<div class="col-10-12 push-1-12">
    <div class="card" style="margin-bottom:20px;">
        <div class="top green">
           Create Clan
        </div>
        <div class="content">
            <div style="width:100%;">
                <form action="{{ route('postCreateClan') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input class="upload-input" style="width:50px;margin-right:5px;display:inline-block;" type="text" name="tag" placeholder="Tag" required value="{{ old('tag') }}">
                    <input class="upload-input" style="display:inline-block;" type="text" name="title" placeholder="Title" required value="{{ old('title') }}">
                    <input class="upload-input" type="file" name="image" style="border:0;" required value="{{ old('img') }}">
                    <textarea class="upload-input" name="description" placeholder="Description" style="width:320px;height:100px;">{{ old('description') }}</textarea>
                    <span style="color:green;display:block;">This will cost <span class="bucks-icon"></span>25</span>
                    <input class="button green upload-submit" type="submit" value="PURCHASE CLAN">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
