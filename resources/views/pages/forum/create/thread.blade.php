@extends('layouts.header')

@section('title', 'Create Thread')

@section('content')
@auth
<div class="col-10-12 push-1-12">
    @include('includes.forumbar', ['bookmarks' => $bookmarks])
</div>
@endauth
<div class="col-10-12 push-1-12">
    <div class="forum-bar weight600" style="padding:10px 5px 10px 0;">
        <a href="/forum/">Forum</a>
        <i class="fa fa-angle-double-right" style="font-size:1rem;" aria-hidden="true"></i>
        <a href="/forum/{{ $board->id }}/">{{ $board->name }}</a>
    </div>
    <div class="card">
        <div class="top {{ $board->category->color }}">
            Create Thread in {{ $board->name }}
        </div>

        <div class="content">
            <form method="POST" id="post-form">
                @csrf
                <input type="text" name="title" placeholder="Title (max 60 characters)" value="{{ old('title') }}" style="width:100%;font-size:16px;box-sizing:border-box;" required>
                <textarea id="body" name="body" placeholder="Body (max 3,000 characters)" style="width:100%;min-height:200px;font-size:16px;box-sizing:border-box;margin-top:10px;" required>{{ old('body') }}</textarea>
                <div style="text-align:center;">
                    <button type="submit" class="button smaller-text {{ $board->category->color }}">
                        Create Thread
                    </button>
                    <button type="button" id="draft" class="button smaller-text {{ $board->category->color }}">
                        Save as Draft
                    </button>
                </div>
            </form>
        </div>
        <script>
        document.getElementById('draft').onclick = (e) => {
            e.preventDefault();
            document.getElementById('post-form').action = '{{ route('createDraft', $board->id) }}'
            document.getElementById('post-form').submit();
        };
        </script>
    </div>
</div>
@endsection
