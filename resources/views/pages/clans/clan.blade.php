@extends('layouts.header')

@section('title', $clan['title'])

@section('content')
<div class="col-10-12 push-1-12">
    <div class="card">
        <div class="top" style="position:relative;">
            @auth
            <dropdown id="dropdown-v" class="dropdown" style="right:7.5px;">
                <ul>
                    @if($isUserInClan)
                    @if($isUserOwner || $userRank->perm_editClan || $userRank->perm_editDesc || $userRank->perm_addDelRank || $userRank->perm_changeRank || $userRank->perm_inviteDecline || $userRank->perm_allyEnemy)
                    <li>
                        <a href="/clan/{{ $clan['id'] }}/edit">Edit</a>
                    </li>
                    @endif
                    @endif
                    <li>
                        <a href="/report/clan/{{ $clan['id'] }}/">Report</a>
                    </li>
                    @can('scrub clans')
                    <li>
                        <form method="POST" action="/clan/{{ $clan->id }}/scrubImage" style="padding:0;">
                            @csrf
                            <a onclick="$(this).parent().submit()">Scrub Image</a>
                        </form>
                    </li>
                    <li>
                        <form method="POST" action="/clan/{{ $clan->id }}/scrubName" style="padding:0;">
                            @csrf
                            <a onclick="$(this).parent().submit()">Scrub Name</a>
                        </form>
                    </li>
                    <li>
                        <form method="POST" action="/clan/{{ $clan->id }}/scrubDesc" style="padding:0;">
                            @csrf
                            <a onclick="$(this).parent().submit()">Scrub Description</a>
                        </form>
                    </li>
                    <li>
                        <form method="POST" action="/clan/{{ $clan->id }}/scrubTag" style="padding:0;">
                            @csrf
                            <a onclick="$(this).parent().submit()">Scrub Tag</a>
                        </form>
                    </li>
                    @endcan
                </ul>
            </dropdown>
            @endauth
            <span class="clan-title">{{ $clan['title'] }}</span><b>[{{ $clan['tag'] }}]</b>
        </div>
        <div class="content" style="position:relative;">
            <div class="col-3-12">
                <div class="clan-img-holder mb1">
                    <img class="width-100" src="{{ $clan->thumbnail }}">
                </div>
                <div class="dark-gray-text bold">
                    <div>
                        Owned by 
                            @if($clan['ownership'] == 'none')
                                Nobody 
                            @elseif($clan['ownership'] == 'user')
                                <b>
                                    <a href="/user/{{ $owner['id'] }}/" class="black-text">{{ $owner['username'] }}</a>
                                </b>
                            @else
                                <b>
                                    <a href="/clan/{{ $owner['id'] }}/" class="black-text">{{ $owner['title'] }}</a>
                                </b>
                            @endif
                    </div>
                    <div>{{ number_format($members) }} {{ str_plural('Member', $members) }}</div>
                </div>
                @auth
                {{-- user is in clan and isnt owner --}}
                @if($isUserInClan && ($clan['ownership'] != 'user' || $clan['owner_id'] != Auth::id()))
                <form method="POST" action="{{ route('joinClan') }}" style="display:inline-block">
                    @csrf
                    <input type="hidden" name="clan_id" value="{{ $clan['id'] }}">
                    <input type="hidden" name="type" value="leave">
                    <button class="red" style="font-size:12px;" type="submit">LEAVE</button>
                </form>
                @endif
                {{-- clan isnt owned --}}
                @if($clan['ownership'] == 'none')
                <form method="POST" action="{{ route('joinClan') }}" style="display:inline-block">
                    @csrf
                    <input type="hidden" name="clan_id" value="{{ $clan['id'] }}">
                    <input type="hidden" name="type" value="claim">
                    <button class="green" style="font-size:12px;width:140px;padding-left:5px;padding-right:5px;" type="submit">CLAIM OWNERSHIP</button>
                </form>
                @endif
                {{-- only show if inow in clan --}}
                @if(!$isUserInClan)
                {{-- show pending or join buttons --}}
                @if($userMember && $userMember['status'] == 'pending')
                <button class="gray" type="submit">JOIN PENDING</button>
                @else
                <form method="POST" action="{{ route('joinClan') }}" class="inline">
                    @csrf
                    <input type="hidden" name="clan_id" value="{{ $clan['id'] }}">
                    <button class="green" style="font-size:12px;" type="submit">JOIN</button>
                </form>
                @endif
                {{-- show primary button --}}
                @elseif(Auth::user()->primary_clan_id != $clan['id'])
                <form method="POST" action="{{ route('makePrimary') }}" class="inline">
                    @csrf
                    <input type="hidden" name="clan_id" value="{{ $clan['id'] }}">
                    <button class="green" style="font-size:12px;width:120px;padding-left:5px;padding-right:5px;" type="submit">MAKE PRIMARY</button>
                </form>
                @elseif(Auth::user()->primary_clan_id == $clan['id'])
                <form method="POST" action="{{ route('makePrimary') }}" class="inline">
                    @csrf
                    <input type="hidden" name="clan_id" value="{{ $clan['id'] }}">
                    <button class="red" style="font-size:12px;width:130px;padding-left:5px;padding-right:5px;" type="submit">REMOVE PRIMARY</button>
                </form>
                @endif
                @endauth
            </div>
            <div class="col-9-12">
                <div class="clan-description darkest-gray-text bold">
                    <span>{!! nl2br(e($clan['description'])) !!}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-1-1 tab-buttons">
        <button class="tab-button blue w600" data-tab="1">MEMBERS</button>
        <button class="tab-button transparent w600" data-tab="2">RELATIONS</button>
        {{-- <button class="tab-button transparent w600" data-tab="3">STORE</button> --}}
    </div>
    <div class="col-1-1">
        <div class="button-tabs">
            <div class="button-tab active" data-tab="1">
                <div class="col-1-1">
                    <div class="card">
                        <div class="top blue">
                            Members
                        </div>
                        <div class="content" style="min-height:250px;">
                            <div class="mb1 overflow-auto">
                                @if($isUserInClan)
                                <span class="dark-gray-text">Your rank: <b class="black-text">{{ $userRank['name'] }}</b></span>
                                @endif
                                <div class="rank-select" style="width:150px;float:right;">
                                    <select class="push-right select">
                                        @foreach($ranks as $rank)
                                        <option value="{{ $rank['rank_id'] }}">{{ $rank['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="member-holder overflow-auto unselectable">
                                </div>
                                <div class="member-pages pages blue unselectable">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="button-tab" data-tab="2">
                <div class="col-1-1">
                    <div class="card">
                        <div class="top">
                            Relations
                        </div>
                        <div class="content">
                            <div>
                                <fieldset class="fieldset green mb1">
                                    <legend>Allies</legend>
                                    <div class="p1 overflow-auto">
                                        @if(count($allyRelations) == 0)
                                        <div class="text-center bold agray-text">This clan has no allies</div>
                                        @endif
                                        @foreach($allyRelations as $relation)
                                        @php
                                        if($relation['from_clan'] == $clan['id']) {
                                            $type = 'tclan';
                                        } else {
                                            $type = 'fclan';
                                        }
                                        @endphp
                                        <a href="/clan/{{ $relation[$type]['id'] }}/">
                                            <div class="profile-card">
                                                <img src="{{ $relation[$type]->thumbnail }}">
                                                <span class="ellipsis">{{ $relation[$type]['title'] }}</span>
                                            </div>
                                        </a>
                                        @endforeach
                                    </div>
                                </fieldset>
                                <fieldset class="fieldset red">
                                    <legend>Enemies</legend>
                                    <div class="p1 overflow-auto">
                                        @if(count($enemyRelations) == 0)
                                        <div class="text-center bold agray-text">This clan has no enemies</div>
                                        @endif
                                        @foreach($enemyRelations as $relation)
                                        @php
                                        if($relation['from_clan'] == $clan['id']) {
                                            $type = 'tclan';
                                        } else {
                                            $type = 'fclan';
                                        }
                                        @endphp
                                        <a href="/clan/{{ $relation[$type]['id'] }}/">
                                            <div class="profile-card">
                                                <img src="{{ $relation[$type]->thumbnail }}">
                                                <span class="ellipsis">{{ $relation[$type]['title'] }}</span>
                                            </div>
                                        </a>
                                        @endforeach
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="button-tab" data-tab="3">
                <div class="col-1-1">
                    <div class="card">
                        <div class="top red">
                            Store
                        </div>
                        <div class="content">
                            Feature coming soon
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function loadMembers(rank = '', page = 1) {
            if(rank == '')
                rank = $('.rank-select option').first().val()
            $.getJSON(`/api/clans/members/{{ $clan['id'] }}/${rank}/${page}`, (data) => {
                $('.member-holder').html();
                let html = '';
                for(let i in data.data) {
                    let user = data.data[i].user
                    html += `<a href="/user/${user.id}/">
                                <div class="col-1-5 mobile-col-1-2">
                                    <img style="width:145px;height:145px;" src="${BH.avatarImg(user.id)}">
                                    <div class="ellipsis">${user.username}</div>
                                </div>
                            </a>`
                }
                $('.member-pages').html();
                let pagehtml = '';
                for(let i of data.pages.pages) {
                    pagehtml += `<a class="page${i == page ? ' active' : ''}">${i}</a>`
                }
                $('.member-pages').html(pagehtml)
                $('.member-holder').html(html)
            })
        }
        $('.rank-select select').on('change', (e) => {
            loadMembers($('option:selected', e.target).val())
        })
        $(document).on('click', '.member-pages a', function() {
            loadMembers($('.rank-select select option:selected').val(), $(this).text())
        })
        loadMembers(1)
    </script>
</div>
@endsection
