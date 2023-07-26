@extends('layouts.header')

@section('title', $draft->title)

@section('content')
@auth
<div class="col-10-12 push-1-12">
    @include('includes.forumbar', ['bookmarks' => $bookmarks])
</div>
@endauth
<div class="col-10-12 push-1-12">
    <div class="forum-bar weight600" style="padding: 5px 5px 5px 0px;">
        <a href="/forum/">Forum</a>
        <i class="fa fa-angle-double-right" style="font-size:1rem;" aria-hidden="true"></i>
        <a href="/forum/draft/{{ $draft->id }}/">{{ $draft->title }}</a>
    </div>
    <div class="card">
        <div class="top {{ $draft->board->category->color }}">
            Draft in {{ $draft->board->name }}
        </div>
        <div class="content">
            <form method="POST" action="{{ route('createThread', [$draft->board->id, $draft->id]) }}" id="post-form">
                @csrf
                <input type="text" name="title" placeholder="Title (max 60 characters)" value="{{ old('title') ?? $draft->title }}" style="width:100%;font-size:16px;box-sizing:border-box;" required>
                <textarea id="body" name="body" placeholder="Body (max 3,000 characters)" style="width:100%;min-height:200px;font-size:16px;box-sizing:border-box;margin-top:10px;" required>{{ old('body') ?? $draft->body }}</textarea>
                <div style="text-align:center;">
                    <button type="submit" class="button smaller-text {{ $draft->board->category->color }}">
                        Create Thread
                    </button>
                    <button type="button" id="draft" class="button smaller-text {{ $draft->board->category->color }}">
                        Save Draft
                    </button>
                </div>
            </form>
            <p id="post-preview" class="post-preview"></p>
        </div>
    </div>
    <script>
    document.getElementById('draft').onclick = (e) => {
        e.preventDefault();
        document.getElementById('post-form').action = '{{ route('updateDraft', $draft->id) }}'
        document.getElementById('post-form').submit();
    };
    </script>
</div>
@endsection
