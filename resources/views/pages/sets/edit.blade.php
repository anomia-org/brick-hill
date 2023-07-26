@extends('layouts.header')

@section('title', 'Edit Set')

@section('content')
<div class="col-10-12 push-1-12">
    <edit-set
        id="editset-v"
        set-name="{{ $set->name }}"
        set-genre="{{ $set->setGenre?->name }}"
        set-description="{{ $set->description }}"
        set-thumbnail="{{ config('site.storage.domain') . $set->thumbnail }}"
        :set-id="{{ $set->id }}"
        init-server-type="{{ $set->is_dedicated ? 'dedicated' : 'nh' }}"
        who-can-join="{{ $set->friends_only ? 'friends' : 'everyone' }}"
        :max-players="{{ $set->max_players ?? 12 }}"
        crash-report="{{ $set->crash_report }}"
        can-use-dedicated="
        @can('updateDedicated', $set)
        1
        @endcan
        "
    ></edit-set>
</div>
@endsection
