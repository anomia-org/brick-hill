@extends('layouts.header')

@section('title', 'Edit Clan')

@section('content')
<div class="col-10-12 push-1-12">
    <div class="tabs">
        <meta name="clan_id" content="{{ $clan['id'] }}">
        <div class="tab @if(session('tab') != 'member')active @endif col-1-2" data-tab="1">
            Edit
        </div>
        @if($rank->perm_changeRank || $rank->perm_allyEnemy || $rank->inviteDecline)
        <div class="tab @if(session('tab') == 'member')active @endif col-1-2" data-tab="2">
            Members & Relations
        </div>
        @endif
        <div class="tab-holder">
            <div class="tab-body @if(session('tab') != 'member')active @endif" data-tab="1">
                    <div class="content p2">
                        <h1 style="font-size:23px;margin-top:0;">Edit {{ $clan['title'] }}</h1>
                        @if($rank->perm_editDesc || $rank->perm_editClan)
                        <div class="flex-container">
                            <div class="clan-edit-icon clan-edit col-3-12">
                                <div class="bold">Change Icon</div>
                                <img src="{{ $clan->thumbnail }}" style="width:150px;height:150px;">
                                <form method="POST" action="/clan/{{ $clan->id }}/thumbnail" enctype="multipart/form-data">
                                    @csrf
                                    <input class="upload-input" type="file" name="image" style="border:0;padding-left:0;" required>
                                    <input class="button blue upload-submit" type="submit" value="UPLOAD">
                                </form>
                            </div>
                            <div class="clan-edit-description clan-edit col-9-12">
                                <div class="bold">Update Description</div>
                                <form method="POST" action="{{ route('editClanPost') }}" style="height:65%;">
                                    @csrf
                                    <input type="hidden" name="type" value="description">
                                    <input type="hidden" name="clan_id" value="{{ $clan['id'] }}">
                                    <textarea class="upload-input" name="description" style="width:90%;height:100%;">{{ !is_null(old('description')) ? old('description') : $clan['description'] }}</textarea>
                                    <input class="button blue upload-submit" type="submit" value="SAVE">
                                </form>
                            </div>
                        </div>
                        @endif
                        @if($rank->perm_editClan)
                        <hr>
                        <div class="overflow-auto">
                            <div class="bold">Join Type</div>
                            <form method="POST" action="{{ route('editClanPost') }}">
                                @csrf
                                <input type="hidden" name="type" value="join_type">
                                <input type="hidden" name="clan_id" value="{{ $clan['id'] }}">
                                <select name="value" class="select">
                                    <option value="open" @if($clan['type'] !== 'private')selected @endif>Open to all</option>
                                    <option value="request" @if($clan['type'] == 'private')selected @endif>Request to join</option>
                                </select>
                                <div></div>
                                <input class="button blue upload-submit" type="submit" value="SAVE">
                            </form>
                        </div>
                        <hr>
                        @endif
                        @if($rank->perm_addDelRank)
                        <div class="clan-edit-ranks overflow-auto">
                            <div class="bold">Edit Ranks</div>
                            <form method="POST" action="{{ route('editClanPost') }}">
                                @csrf
                                <input type="hidden" name="type" value="edit_ranks">
                                <input type="hidden" name="clan_id" value="{{ $clan['id'] }}">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <h5>Power</h5>
                                            </td>
                                            <td>
                                                <h5>Name</h5>
                                            </td>
                                            @php
                                            $checkArr = [
                                                'perm_postWall' => 'Let these users post to the wall',
                                                'perm_modWall' => 'Let these users moderate the wall',
                                                'perm_inviteDecline' => 'Let these users invite/reject users',
                                                'perm_allyEnemy' => 'Let these users ally/enemy other clans',
                                                'perm_changeRank' => 'Let these users rank other users',
                                                'perm_addDelRank' => 'Let these users add/delete ranks',
                                                'perm_editDesc' => 'Let these users edit the clan description',
                                                'perm_shoutBox' => 'Let these users use the shoutout box',
                                                'perm_addFunds' => 'Let these users add funds to the clan',
                                                'perm_takeFunds' => 'Let these users take funds from the clan',
                                                'perm_editClan' => 'Let these users edit the clan'
                                            ];
                                            @endphp
                                            @foreach($checkArr as $check)
                                            <td>
                                                <h5 class="text-center"><i class="fa fa-info-circle" title="{{ $check }}"></i></h5>
                                            </td>
                                            @endforeach
                                        </tr>
                                        @foreach ($ranks as $clanRank)
                                        <tr>
                                            <td>
                                                @if($clanRank['rank_id'] == 100 || $clanRank['rank_id'] >= $rank['rank_id'])
                                                <input disabled type="number" name="rank{{ $clanRank['rank_id'] }}power" value="{{ $clanRank['rank_id'] }}" style="width:65px;">
                                                @else
                                                <input min="1" max="99" type="number" name="rank{{ $clanRank['rank_id'] }}power" value="{{ $clanRank['rank_id'] }}">
                                                @endif
                                            </td>
                                            <td>
                                                <input @if($clanRank['rank_id'] >= $rank['rank_id'] && $rank['rank_id'] !== 100)disabled @endif type="text" name="rank{{ $clanRank['rank_id'] }}name" value="{{ $clanRank['name'] }}">
                                            </td>
                                            @foreach($checkArr as $perm => $val)
                                            <td>
                                                <input type="checkbox" name="rank{{ $clanRank['rank_id'] }}{{ $perm }}" @if($clanRank[$perm]) checked @endif @if($clanRank['rank_id'] == 100 || $clanRank['rank_id'] >= $rank['rank_id']) disabled @endif>
                                            </td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <input class="button blue upload-submit" type="submit" value="SAVE">
                            </form>
                        </div>
                        <hr>
                        <div class="clan-new-rank clan-edit overflow-auto">
                            <div class="bold">New Rank</div>
                            <form method="POST" action="{{ route('editClanPost') }}">
                                @csrf
                                <input type="hidden" name="type" value="new_rank">
                                <input type="hidden" name="clan_id" value="{{ $clan['id'] }}">
                                <input type="text" name="new_rank_name" placeholder="Rank name">
                                <div class="bucks-text bold">This will cost <span class="bucks-icon"></span>6</div>
                                <input class="button blue upload-submit" type="submit" value="CREATE" style="display: block;">
                            </form>
                        </div>
                        <hr>
                        @endif
                        @if($clan['ownership'] == 'user' && $clan['owner_id'] == Auth::id())
                        <div class="clan-change-owner overflow-auto clan-edit">
                            <div class="bold">Ownership</div>
                            <form method="POST" action="{{ route('editClanPost') }}">
                                @csrf
                                <input type="hidden" name="type" value="ownership">
                                <input type="hidden" name="clan_id" value="{{ $clan['id'] }}">
                                <input type="text" name="username" placeholder="Username">
                                <div class="red-text bold">User must be in clan to be given ownership</div>
                                <input class="button blue upload-submit" type="submit" value="TRANSFER">
                            </form>
                        </div>
                        <hr>
                        <div class="clan-change-owner clan-edit">
                            <div class="bold">Abandon</div>
                            <form class="overflow-auto" method="POST" action="{{ route('editClanPost') }}">
                                @csrf
                                <input type="hidden" name="type" value="abandon">
                                <input type="hidden" name="clan_id" value="{{ $clan['id'] }}">
                                <input class="button red upload-submit" type="submit" value="ABANDON">
                            </form>
                            <div class="red-text bold">THIS CANNOT BE UNDONE</div>
                        </div>
                        @endif
                    </div>
            </div>
            @if($rank->perm_changeRank || $rank->perm_allyEnemy || $rank->inviteDecline)
            <div class="tab-body @if(session('tab') == 'member')active @endif" data-tab="2">
                <div class="p1">
                    <div class="content">
                        @if($rank->perm_changeRank)
                        <h1 style="font-size:23px;margin-top:0;">Members & Relations</h1>
                        <div class="clan-change-ranks clan-edit rank-select">
                            <div class="bold">Members</div>
                            <select class="select" style="width:150px;" id="member-rank" onchange="loadEditMembers()">
                                @foreach($ranks as $rank)
                                <option value="{{ $rank['rank_id'] }}">{{ $rank['name'] }}</option>
                                @endforeach
                            </select>
                            <div class="overflow-auto edit-holder unselectable">
                            </div>
                            <div class="pages unselectable">
                            </div>
                        </div>
                        <script>
                            $(document).on('change', 'select[data-user]', function() {
                                let select = $(this);
                                let user = select.data('user');
                                $.post(`{{ route('editClanPost') }}`, {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    user_id: user,
                                    clan_id: {{ $clan['id'] }},
                                    type: 'change_rank',
                                    new_rank: $(':selected', select).val()
                                }, () => {loadEditMembers($('.member-pages .forumPage.blue').text() || 1)})
                                .fail((data) => {
                                    $('.col-10-12.push-1-12').prepend(`
                                        <div class="error-notification">
                                            ${data.responseJSON.error}
                                        </div>
                                    `);
                                });
                            })
                            $(document).on('click', '#clan-search', searchRelationClans);
                            $(document).on('keyup', '#clan-search-bar', searchRelationClans);
                            loadEditMembers();
                        </script>
                        <hr>
                        @endif
                        @if($rank->perm_inviteDecline)
                        @if($clan['type'] == 'private' || count($pendingMembers)  > 0)
                        <div class="clan-pending-members clan-edit">
                            <div class="bold">Pending Members</div>
                            <div class="pending-holder" style="padding-top:5px;">
                                @if(count($pendingMembers) == 0)
                                You have no pending members
                                @else
                                @foreach($pendingMembers as $member)
                                <a href="/user/{{ $member['user']['id'] }}/">
                                    <div class="profile-card" style="width:170px;padding-top:5px;padding-bottom:5px;">
                                        <img src="{{ $member->user->avatar_thumbnail }}" style="width:115px;height:115px;">
                                        <span class="ellipsis">{{ $member['user']['username'] }}</span>
                                        <form method="POST" action="{{ route('editClanPost') }}">
                                            @csrf
                                            <input type="hidden" name="type" value="pending_member">
                                            <input type="hidden" name="user_id" value="{{ $member['user_id'] }}">
                                            <input type="hidden" name="clan_id" value="{{ $clan['id'] }}">
                                            <button class="button small green" name="accept" value="accept">ACCEPT</button>
                                            <button class="button small red" name="decline" value="decline">DECLINE</button>
                                        </form>
                                    </div>
                                </a>
                                @endforeach
                                @endif
                            </div>
                        </div>
                        <hr>
                        @endif
                        @endif
                        @if($rank->perm_allyEnemy)
                        <div class="clan-ally-enemy clan-edit">
                            <div class="bold">Allies and Enemies</div>
                            <input type="text" name="clan-search" placeholder="Search clans" id="clan-search-bar">
                            <button class="button small blue smaller-text small" id="clan-search">SEARCH</button>
                            <div class="relation-holder" style="padding-top:5px;">
                            </div>
                            <hr>
                            <div class="bold">Pending Allies</div>
                            <div class="pending-allies @if(count($pendingAllies) != 0) 'text-center' @endif">
                                @if(count($pendingAllies) == 0)
                                You have no pending allies
                                @else
                                @foreach($pendingAllies as $relation)
                                <a href="/clan/{{ $relation['fclan']['id'] }}/">
                                    <div class="profile-card" style="width:170px;padding-top:5px;padding-bottom:5px;">
                                        <img src="{{ $relation['fclan']->thumbnail }}">
                                        <span class="ellipsis">{{ $relation['fclan']['title'] }}</span>
                                        <form method="POST" action="{{ route('editClanPost') }}">
                                            @csrf
                                            <input type="hidden" name="type" value="pending_ally">
                                            <input type="hidden" name="from_clan" value="{{ $relation['fclan']['id'] }}">
                                            <input type="hidden" name="clan_id" value="{{ $clan['id'] }}">
                                            <button class="button small green" name="accept" value="accept">ACCEPT</button>
                                            <button class="button small red" name="decline" value="decline">DECLINE</button>
                                        </form>
                                    </div>
                                </a>
                                @endforeach
                                @endif
                            </div>
                            <span style="font-weight:600;display:block;">Pending enemies</span>
                            <div class="pending-enemies"@if(count($pendingEnemies) != 0)style="text-align:center;"@endif>
                                @if(count($pendingEnemies) == 0)
                                You have no pending enemies
                                @else
                                @foreach($pendingEnemies as $relation)
                                <a href="/clan/{{ $relation['fclan']['id'] }}/">
                                    <div class="profile-card" style="width:170px;padding-top:5px;padding-bottom:5px;">
                                        <img src="{{ $relation['fclan']->thumbnail }}">
                                        <span class="ellipsis">{{ $relation['fclan']['title'] }}</span>
                                        <form method="POST" action="{{ route('editClanPost') }}">
                                            @csrf
                                            <input type="hidden" name="type" value="pending_ally">
                                            <input type="hidden" name="from_clan" value="{{ $relation['fclan']['id'] }}">
                                            <input type="hidden" name="clan_id" value="{{ $clan['id'] }}">
                                            <button class="button small green" name="accept" value="accept">ACCEPT</button>
                                            <button class="button small red" name="decline" value="decline">DECLINE</button>
                                        </form>
                                    </div>
                                </a>
                                @endforeach
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
