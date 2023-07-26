@extends('layouts.header')

@section('title', 'Clans')

@section('content')
<div class="col-10-12 push-1-12">
    @auth
    @if(count($user_clans) > 0)
    <div class="card">
        <div class="top blue">
            My Clans
        </div>
        <div class="content" style="text-align:center;">
            <div class="carousel clans">
                <div style="width:95%;margin-right:auto;margin-left:auto;overflow:hidden">
                    <ul style="max-height: 160px;">
                        @foreach ($user_clans as $clan)
                        <li class="carousel li" data-iteration="{{ $loop->iteration }}">
                            <a href="/clan/{{ $clan['clan']['id'] }}/">
                                <div class="profile-card">
                                    <img src="{{ $clan['clan']->thumbnail }}">
                                    <span class="ellipsis">{{ $clan['clan']['title'] }}</span>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                {{-- only add the script if there should be a carousel because it will mess with aligning if it is there --}}
                @if(count($user_clans) > 6)
                <script>
                    $('.carousel ul').slick({
                        infinite: true,
                        slidesToShow: 6,
                        slidesToScroll: 1,
                        speed: 250,
                        prevArrow: '<a class="carousel-button left"><i class="fas fa-angle-left"></i></a>',
                        nextArrow: '<a class="carousel-button right"><i class="fas fa-angle-right"></i></a>'
                    });
                </script>
                @endif
            </div>
        </div>
    </div>
    @endif
    @endauth
    <div class="card">
        <div class="top blue">
            Popular Clans
        </div>
        <div class="content">
            <div class="mb2 overflow-auto">
                <div class="col-9-12">
                    <input type="text" style="height:41px;" class="width-100 rigid" placeholder="Search">
                </div>
                <div class="col-3-12">
                    <div class="acc-1-2 np">
                        <button class="button blue width-100">Search</button>
                    </div>
                    @auth
                    <div class="acc-1-2 np">
                        <a href="/clans/create" class="button green width-100">Create</a>
                    </div>
                @endauth
                </div>
            </div>
            <div class="col-1-1" style="padding-right:0;">
                @if(count($clans) == 0)
                <div style="text-align:center">
                    <span>No clans found</span>
                </div>
                @endif
                @foreach ($clans as $clan)
                    <a href="/clan/{{ $clan['id'] }}/">
                        <div class="hover-card clan">
                            <div class="clan-logo">
                                <img class="width-100" src="{{ $clan->thumbnail }}">
                            </div>
                            <div class="data ellipsis">
                                <span class="clan-name bold mobile-col-1-2 ellipsis">{{ $clan['title'] }}</span>
                                <span class="push-right">{{ number_format($clan['total']) }} {{ str_plural('Member', $clan['total']) }}</span>
                            </div>
                            <div class="clan-description">
                                {!! nl2br(e($clan['description'])) !!}
                            </div>
                        </div>
                    </a>
                    <hr>
                @endforeach
            </div>
            <div class="pages">
                @if(isset($pages['pages']))
                    @foreach ($pages['pages'] as $pageNum)
                        <a class="page @if($pages['current'] == $pageNum) active @endif" @if($pages['current'] != $pageNum) @endif href="/clans/{{ $pageNum }}/{{ $search }}">{{ $pageNum }}</a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <script>
        $(document).on('click', '.blue.button', searchClans);
        $(document).on('keyup', '.rigid', searchClans);

        function searchClans(e) {
            if($(e.target).hasClass('button') || event.keyCode == 13) {
                let search = $('input.rigid').val()

                window.location = `{{ route('clanView', ['page' => 1]) }}/${search}`;
            }
        }
    </script>
</div>
@endsection
