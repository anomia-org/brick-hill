<?php

namespace App\Http\Controllers\Clan;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

use App\Http\Requests\Clan\UploadThumbnail;
use App\Helpers\Assets\Uploader;
use Carbon\Carbon;

use App\Models\Polymorphic\AssetType;
use App\Models\User\User;
use App\Models\Clan\{
    Clan,
    ClanRank,
    ClanMember,
    ClanRelation
};

class EditController extends Controller
{
    use \App\Traits\Controllers\PostLimit;

    public function editClan($id)
    {
        $clan = Clan::find($id);

        if (!$clan)
            return redirect(route('clanView'));

        $userMember = ClanMember::where([['clan_id', $id], ['user_id', Auth::id()], ['status', 'accepted']])
            ->first();

        if (!$userMember)
            return redirect(route('clanView'));

        $rank = ClanRank::where([['rank_id', $userMember['rank']], ['clan_id', $id]])
            ->first();

        if (!$rank)
            return redirect(route('clanView'));

        // give owner all perms even if they are not in the clan (prevents bugs)
        if ($clan->owner_id == Auth::id()) {
            $rank->perm_editClan = true;
            $rank->perm_editDesc = true;
            $rank->perm_addDelRank = true;
            $rank->perm_changeRank = true;
            $rank->perm_inviteDecline = true;
            $rank->perm_allyEnemy = true;
        }

        if ($clan->owner_id == Auth::id() || $rank->perm_editClan || $rank->perm_editDesc || $rank->perm_addDelRank || $rank->perm_changeRank || $rank->perm_inviteDecline || $rank->perm_allyEnemy) {
            $clanRanks = ClanRank::where('clan_id', $id)->orderBy('rank_id', 'ASC')->get();

            $pendingAllies = ClanRelation::where([['to_clan', $id], ['status', 'pending'], ['relation', 'ally']])->get();
            $pendingEnemies = ClanRelation::where([['to_clan', $id], ['status', 'pending'], ['relation', 'enemy']])->get();
            $pendingMembers = ClanMember::where([['clan_id', $id], ['status', 'pending']])->get();
            return view('pages.clans.edit')->with([
                'clan' => $clan,
                'member' => $userMember,
                'rank' => $rank,
                'ranks' => $clanRanks,
                'pendingAllies' => $pendingAllies,
                'pendingEnemies' => $pendingEnemies,
                'pendingMembers' => $pendingMembers
            ]);
        } else {
            return redirect(route('clanView'));
        }
    }

    public function uploadThumbnail(UploadThumbnail $request, Clan $clan, Uploader $uploader)
    {
        $userMember = ClanMember::where([['clan_id', $clan->id], ['user_id', Auth::id()], ['status', 'accepted']])
            ->first();
        $userRank = ClanRank::where([['clan_id', $clan->id], ['rank_id', $userMember['rank']]])
            ->first();
        $perms = $this->getPermissions($clan, $userRank);

        if (!in_array('edit_clan', $perms))
            throw new \App\Exceptions\BaseException('You do not have permission to update the thumbnail');

        if (!$this->canMakeNewPost($clan->assets(), 60))
            throw new \App\Exceptions\BaseException('You can only change the thumbnail once every 60 seconds');

        $asset = $uploader->upload($request->image);
        $asset->asset_type_id = AssetType::type('image')->firstOrFail()->id;
        $asset->creator_id = Auth::id();
        $clan->assets()->save($asset);

        return redirect(route('editClan', ['id' => $clan->id]));
    }

    public function editClanPost()
    {
        $route = route('editClan', ['id' => request('clan_id')]);
        if (!request('type'))
            return redirect($route);
        $type = request('type');
        $user = Auth::user();

        $clan = Clan::find(request('clan_id'));
        if (!$clan)
            return redirect(route('clanView'));

        $id = request('clan_id');

        $userMember = ClanMember::where([['clan_id', $id], ['user_id', Auth::id()], ['status', 'accepted']])
            ->first();
        $userRank = ClanRank::where([['clan_id', $id], ['rank_id', $userMember['rank']]])
            ->first();
        $perms = $this->getPermissions($clan, $userRank);

        $permArr = [
            'perm_postWall',
            'perm_modWall',
            'perm_inviteDecline',
            'perm_allyEnemy',
            'perm_changeRank',
            'perm_addDelRank',
            'perm_editDesc',
            'perm_shoutBox',
            'perm_addFunds',
            'perm_takeFunds',
            'perm_editClan'
        ];
        switch ($type) {
            case 'description':
                if (in_array('edit_clan', $perms) || in_array('description', $perms)) {
                    $rules = [
                        'description' => 'max:10000'
                    ];
                    $this->validator($rules, $route);
                    $clan->description = request('description');
                    $clan->save();
                    return success('Description updated');
                }
                break;
            case 'join_type':
                if (in_array('edit_clan', $perms)) {
                    $rules = [
                        'value' => [
                            'required',
                            Rule::in(['open', 'request'])
                        ]
                    ];
                    $this->validator($rules, $route);
                    if (request('value') == 'open') {
                        $clan->type = 'open';
                    } else {
                        $clan->type = 'private';
                    }
                    $clan->save();
                    return success('Join type changed');
                }
                break;
            case 'edit_ranks':
                if (in_array('edit_clan', $perms) || in_array('edit_ranks', $perms)) {
                    $clanRanks = ClanRank::where('clan_id', $id)
                        ->get();
                    foreach ($clanRanks as $rank) {
                        // block users from editing their own rank
                        if ($rank['rank_id'] < $userRank['rank_id'] || $userRank['rank_id'] == 100) {
                            $permUpdates = [];
                            // change the permissions
                            foreach ($permArr as $perm) {
                                $permUpdates[$perm] = (request('rank' . $rank['rank_id'] . $perm) == 'on' || $rank['rank_id'] == 100) ? 1 : 0;
                            }
                            // make sure the rank isnt already used, isnt for rank 100, or is above rank 100 (or below 1)
                            // also changed the members to be the correct rank
                            if (request('rank' . $rank['rank_id'] . 'power') != $rank['rank_id'] && $rank['rank_id'] !== 100) {
                                $newPower = request('rank' . $rank['rank_id'] . 'power');
                                if ($newPower > 100)
                                    $newPower = 100;
                                if ($newPower < 1)
                                    $newPower = 1;
                                $checkIfExists = ClanRank::where([['clan_id', $id], ['rank_id', $newPower]])
                                    ->first();
                                if ($checkIfExists) {
                                    return error('Rank id must be unique, less than 100, and greater than 0');
                                }
                                $updateMembers = ClanMember::where([['clan_id', $id], ['rank', $rank['rank_id']]])
                                    ->update(['rank' => $newPower]);
                                $permUpdates['rank_id'] = $newPower;
                            }
                            // make sure the name fits in the allowed length
                            $name = request('rank' . $rank['rank_id'] . 'name');
                            if (strlen($name) > 50 || strlen($name) < 2) {
                                return error('Rank name must be between 2 and 50 characters');
                            }
                            $permUpdates['name'] = request('rank' . $rank['rank_id'] . 'name');
                            $rank->update($permUpdates);
                        }
                    }
                    return success('Ranks changed');
                }
                break;
            case 'new_rank':
                if (in_array('edit_clan', $perms) || in_array('edit_ranks', $perms)) {
                    $rules = [
                        'new_rank_name' => 'required|min:2|max:50'
                    ];
                    $this->validator($rules, $route);
                    $newPower = 1;
                    $user = Auth::user();
                    $name = request('new_rank_name');
                    if ($user->bucks < 6) {
                        return error('You need atleast 6 bucks to make a new rank');
                    }
                    $rankCount = ClanRank::where('clan_id', $id)
                        ->count();
                    if ($rankCount >= 100) {
                        return error('You can only have 100 ranks');
                    }
                    $checkIfExists = ClanRank::where([['clan_id', $id], ['rank_id', $newPower]])
                        ->first();
                    if ($checkIfExists) {
                        do {
                            $getNextHighest = ClanRank::where([['clan_id', $id], ['rank_id', '>', $newPower]])
                                ->orderBy('rank_id', 'ASC')
                                ->first();
                            ++$newPower;
                        } while ($getNextHighest['rank_id'] == $newPower);
                    }
                    ClanRank::create([
                        'clan_id' => $id,
                        'rank_id' => $newPower,
                        'name' => request('new_rank_name')
                    ]);
                    $user->bucks = $user->bucks - 6;
                    $user->save();
                    return success("Rank $name created");
                }
                break;
            case 'ownership':
                if ($userRank['rank_id'] == 100) {
                    $username = request('username');
                    $user = User::where('username', $username)
                        ->first();
                    if (!$user) {
                        return error('User does not exist');
                    }
                    $member = ClanMember::where([['clan_id', $id], ['user_id', $user['id'], ['status', 'accepted']]])
                        ->first();
                    if (!$member) {
                        return error('User is not in this clan');
                    }
                    $getNextLowestRank = ClanRank::where([['clan_id', $id], ['rank_id', '!=', 100]])
                        ->orderBy('rank_id', 'DESC')
                        ->first();
                    $userMember->rank = $getNextLowestRank['rank_id'];
                    $userMember->save();
                    $member->rank = 100;
                    $member->save();
                    $clan->ownership = 'user';
                    $clan->owner_id = $user['id'];
                    $clan->save();
                    return success("User $username now owns the clan");
                }
                break;
            case 'abandon':
                if ($clan['ownership'] == 'user' && $clan['owner_id'] == Auth::id()) {
                    $getNextLowestRank = ClanRank::where([['clan_id', $id], ['rank_id', '!=', 100]])
                        ->orderBy('rank_id', 'DESC')
                        ->first();
                    $userMember->rank = $getNextLowestRank['rank_id'];
                    $userMember->save();
                    $clan->ownership = 'none';
                    $clan->save();
                    return redirect()
                        ->route('clan', ['id' => $clan->id]);
                }
                break;
            case 'change_rank':
                if (in_array('change_ranks', $perms)) {
                    $userid = request('user_id');
                    if ($userid == Auth::id())
                        return JSONErr('You can\'t change your own rank');
                    $newRank = request('new_rank');
                    if ($newRank >= $userRank['rank_id'])
                        return JSONErr('You can only edit lower ranks');
                    $userMember = ClanMember::where([['clan_id', $id], ['user_id', $userid], ['status', 'accepted']])
                        ->first();
                    if (!$userMember)
                        return JSONErr('User is not in clan');
                    if ($userMember['rank'] == 100 || $userMember['rank'] >= $userRank['rank_id'])
                        return JSONErr('You can only edit lower ranks');
                    $checkForRank = ClanRank::where([['clan_id', $id], ['rank_id', $newRank]])
                        ->first();
                    if (!$checkForRank)
                        return JSONErr('Rank does not exist');
                    $userMember->rank = $newRank;
                    $userMember->save();
                    return response()->json(['success' => 'Rank changed']);
                }
                return JSONErr('You don\'t have permission');
            case 'kick_user':
                if (in_array('change_ranks', $perms)) {
                    $userid = request('user_id');
                    if ($userid == Auth::id())
                        return redirect($route)
                            ->with('tab', 'member')
                            ->withErrors(['errors' => 'You can\'t kick yourself']);
                    $userMember = ClanMember::where([['clan_id', $id], ['user_id', $userid], ['status', 'accepted']])
                        ->first();
                    if (!$userMember)
                        return redirect($route)
                            ->with('tab', 'member')
                            ->withErrors(['errors' => 'User is not in clan']);
                    if ($userMember['rank'] == 100 || $userMember['rank'] >= $userRank['rank_id'])
                        return redirect($route)
                            ->with('tab', 'member')
                            ->withErrors(['errors' => 'You can only kick from lower ranks']);
                    $userMember->status = 'declined';
                    $userMember->save();
                    return redirect($route)
                        ->with('tab', 'member')
                        ->with('success', 'User has been kicked');
                }
                break;
            case 'pending_member':
                if (in_array('accept_members', $perms)) {
                    $userid = request('user_id');
                    $userMember = ClanMember::where([['clan_id', $id], ['user_id', $userid], ['status', 'pending']])
                        ->first();
                    if (!$userMember)
                        return redirect($route)
                            ->with('tab', 'member')
                            ->withErrors(['errors' => 'User is not pending']);

                    if (request('accept')) {
                        $userMember->status = 'accepted';
                    } else {
                        $userMember->status = 'declined';
                    }
                    $userMember->save();
                    return redirect($route)
                        ->with('tab', 'member')
                        ->with('success', 'User has been ' . $userMember->status);
                }
                break;
            case 'new_relation':
                if (in_array('allies', $perms)) {
                    $toclan = request('to_id');
                    $userClan = Clan::find($toclan);
                    if (!$userClan || $toclan == $id)
                        return redirect($route)
                            ->with('tab', 'member')
                            ->withErrors(['errors' => 'Clan does not exist']);
                    $clanName = $userClan->title;
                    $relation = ClanRelation::where([['to_clan', $toclan], ['from_clan', $id]])
                        ->orWhere([['from_clan', $toclan], ['to_clan', $id]])
                        ->first();
                    if (request('ally')) {
                        $type = 'sent an ally request';
                        $save = 'ally';
                        $stat = 'pending';
                    } else {
                        $type = 'enemied';
                        $save = 'enemy';
                        $stat = 'accepted';
                    }
                    // need to create a new one
                    if (!$relation) {
                        ClanRelation::create([
                            'from_clan' => $id,
                            'to_clan' => $toclan,
                            'relation' => $save,
                            'status' => $stat
                        ]);
                    } else {
                        if (request('ally') && $relation->relation == 'ally' && $relation->status == 'accepted') {
                            return redirect($route)
                                ->with('tab', 'member')
                                ->withErrors(['errors' => 'Clans are already allied']);
                        }
                        if (request('ally')) {
                            $relation->relation = $save;
                            $relation->status = 'pending';
                            $relation->save();
                        } else {
                            $relation->relation = $save;
                            $relation->status = 'accepted';
                            $relation->save();
                        }
                    }
                    return redirect($route)
                        ->with('tab', 'member')
                        ->with('success', "$clanName has been $type");
                }
                break;
            case 'pending_ally':
                if (in_array('allies', $perms)) {
                    $fromclan = request('from_clan');
                    $userClan = Clan::find($fromclan);
                    if (!$userClan)
                        return redirect($route)
                            ->with('tab', 'member')
                            ->withErrors(['errors' => 'Clan does not exist']);
                    $relation = ClanRelation::where([['from_clan', $fromclan], ['to_clan', $id], ['status', 'pending']])
                        ->first();
                    if (request('accept')) {
                        $relation->status = 'accepted';
                        $relation->save();
                    } else {
                        $relation->status = 'declined';
                        $relation->save();
                    }
                    return redirect($route)
                        ->with('tab', 'member')
                        ->with('success', "Ally has been $relation->status");
                }
                break;
            default:
                return redirect($route);
        }
        return error('You do not have necessary permissions');
    }

    private function getPermissions(Clan $clan, ClanRank $userRank): array
    {
        $perms = [];
        // give owner all perms even if they are not in the clan (prevents bugs)
        if ($clan->owner_id == Auth::id()) {
            $userRank->perm_editClan = true;
            $userRank->perm_editDesc = true;
            $userRank->perm_addDelRank = true;
            $userRank->perm_changeRank = true;
            $userRank->perm_inviteDecline = true;
            $userRank->perm_allyEnemy = true;
        }

        if ($userRank->perm_allyEnemy) {
            array_push($perms, 'allies');
        }
        if ($userRank->perm_changeRank) {
            array_push($perms, 'change_ranks');
        }
        if ($userRank->perm_addDelRank) {
            array_push($perms, 'edit_ranks');
        }
        if ($userRank->perm_editDesc) {
            array_push($perms, 'description');
        }
        if ($userRank->perm_editClan) {
            array_push($perms, 'edit_clan');
        }
        if ($userRank->perm_inviteDecline) {
            array_push($perms, 'accept_members');
        }

        return $perms;
    }

    private function validator($rules, $route)
    {
        $validator = validator(request()->all(), $rules);
        if ($validator->fails()) {
            $redir = redirect($route)
                ->withInput()
                ->withErrors(['errors' => $validator->messages()->first()]);
            \Session::driver()->save();
            $redir->send();
        }
    }
}
