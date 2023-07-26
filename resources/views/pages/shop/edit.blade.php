@extends('layouts.header')

@section('title', 'Edit Item')

@section('content')
<edit-item 
    id="edititem-v" 
    init-name="{{ $item->name }}" 
    init-description="{{ $item->description }}"
    init-bucks="{{ $item->bucks }}"
    init-bits="{{ $item->bits }}"
    init-offsale="{{ ($item->offsale || !$item->is_approved) ? "true" : "false" }}"
></edit-item>
@endsection
