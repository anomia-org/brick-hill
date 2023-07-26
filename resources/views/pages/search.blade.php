@extends('layouts.header')

@section('title', 'Search')

@section('content')
<div class="col-10-12 push-1-12 new-theme">
    <div>
        <div class="content">
            <div class="header mb-10">Search Users</div>
            <div class="col-1-1 mb-20" style="text-align:center;padding-right:0;">
                <div class="flex flex-horiz-center mobile-column" style="width:100%">
                    <input style="margin-right:5px;" class="width-100 mb-10-mobile input rigid" type="text" placeholder="Search users" autocomplete="off" value="{{ $search }}">
                    <div class="flex flex-horiz-center" style="margin-left:5px">
                        <button style="height:100%" class="blue shop-search-button">SEARCH</button>
                        <a href="/searchonline" class="button bold green" style="margin-left:10px;">ONLINE</a>
                    </div>
                </div>
            </div>
            <div class="col-1-1 mb-10" style="padding-right:0;">
                @if(count($data['data']) == 0)
                <div style="text-align:center">
                    <span>No users found</span>
                </div>
                @endif
                @foreach ($data['data'] as $user)
                    <a href="/user/{{ $user['id'] }}" class="mb-10">
                        <div class="search-user-card flex">
                            <img src="{{ $user->avatar_thumbnail }}">
                            <div class="width-100 data ellipsis" style="margin-left:10px;height:100%">
                                <div>
                                    <b>{{ $user['username'] }}</b>
                                    <span style="float:right;" class="status-dot @if($user->is_online)online @endif"></span>
                                </div>
                                <p class="data mt-2 description">{!! nl2br(e($user['description'])) !!}</p>
                            </div>
                        </div>
                        <div class="divider mb-20"></div>
                    </a>
                @endforeach
            </div>
            <div class="pages" style="margin-top:20px;">
                @if(request()->cursor)
                <a
                    class="button blue small"
                    style="margin-left: 5px"
                    onclick="history.back()"
                >
                    <i class="fas fa-chevron-left"></i>
                </a>
                @endif
                @if(!is_null($data['next_cursor']))
                    @if($online)
                    <a
                        class="button blue small"
                        style="margin-left: 5px"
                        href="/searchonline/{{ $search }}?cursor={{ $data['next_cursor'] }}"
                    >
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    @else
                    <a
                        class="button blue small"
                        style="margin-left: 5px"
                        href="/search/{{ $search }}?cursor={{ $data['next_cursor'] }}"
                    >
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    @endif
                @endif
            </div>
        </div>
    </div>
    <script>
        $(document).on('click', '.blue.shop-search-button', searchUsers);
        $(document).on('keyup', '.input.rigid', searchUsers);

        function searchUsers(e) {
            if($(e.target).hasClass('shop-search-button') || event.keyCode == 13) {
                let search = $('.input.rigid').val()
                @if($online)
                window.location = `/searchonline/${search}`;
                @else
                window.location = `/search/${search}`;
                @endif
            }
        }
    </script>
</div>
@endsection
