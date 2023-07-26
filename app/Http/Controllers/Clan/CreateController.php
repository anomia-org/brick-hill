<?php

namespace App\Http\Controllers\Clan;

use App\Helpers\Assets\Uploader;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Helpers\Helper;
use App\Http\Requests\Clan\CreateClan;
use Carbon\Carbon;

use App\Models\User\User;
use App\Models\Clan\{
    Clan,
    ClanRank,
    ClanMember,
    ClanRelation
};
use App\Models\Polymorphic\AssetType;

class CreateController extends Controller
{
    public function createClan() {
        return view('pages.clans.create');
    }

    public function createClanPost(CreateClan $request, Uploader $uploader) {
        $clans = Clan::where('title', $request->title)->count();

        if($clans > 0)
            return error('Clan name already taken');

        $user = Auth::user();

        $getUserClans = Clan::where([['ownership', 'user'], ['owner_id', Auth::id()]])
            ->count();
        if($getUserClans >= auth()->user()->membership_limits->create_clans) {
            return error('You can only own '.auth()->user()->membership_limits->create_clans.' '.str_plural('clan', auth()->user()->membership_limits->create_clans).'!');
        }

        if($user['bucks'] < 25)
            return error('You need atleast 25 bucks to make a clan');

        $clan = Clan::create([
            'owner_id' => $user['id'],
            'tag' => $request->tag,
            'title' => $request->title,
            'description' => $request->description ?? '',
            'ownership' => 'user',
            'funds' => 0
        ]);

        $clan->clan_hash = md5($clan->id.$clan->updated_at);
        $clan->save();

        ClanMember::create([
            'clan_id' => $clan->id,
            'user_id' => $user['id'],
            'rank' => 100,
            'status' => 'accepted'
        ]);

        ClanRank::create([
            'clan_id' => $clan->id,
            'rank_id' => 100,
            'name' => 'Owner',
            'perm_postWall' => 1,
            'perm_modWall' => 1,
            'perm_inviteDecline' => 1,
            'perm_allyEnemy' => 1,
            'perm_changeRank' => 1,
            'perm_addDelRank' => 1,
            'perm_editDesc' => 1,
            'perm_shoutBox' => 1,
            'perm_addFunds' => 1,
            'perm_takeFunds' => 1,
            'perm_editClan' => 1
        ]);

        ClanRank::create([
            'clan_id' => $clan->id,
            'rank_id' => 75,
            'name' => 'Admin',
            'perm_postWall' => 1,
            'perm_modWall' => 1,
            'perm_inviteDecline' => 0,
            'perm_allyEnemy' => 0,
            'perm_changeRank' => 0,
            'perm_addDelRank' => 0,
            'perm_editDesc' => 0,
            'perm_shoutBox' => 1,
            'perm_addFunds' => 0,
            'perm_takeFunds' => 0,
            'perm_editClan' => 0
        ]);

        ClanRank::create([
            'clan_id' => $clan->id,
            'rank_id' => 1,
            'name' => 'Member',
            'perm_postWall' => 1,
            'perm_modWall' => 0,
            'perm_inviteDecline' => 0,
            'perm_allyEnemy' => 0,
            'perm_changeRank' => 0,
            'perm_addDelRank' => 0,
            'perm_editDesc' => 0,
            'perm_shoutBox' => 0,
            'perm_addFunds' => 0,
            'perm_takeFunds' => 0,
            'perm_editClan' => 0
        ]);

        $user->decrement('bucks', 25);

        $asset = $uploader->upload($request->image);
        $asset->asset_type_id = AssetType::type('image')->firstOrFail()->id;
        $asset->creator_id = Auth::id();
        $clan->assets()->save($asset);

        return redirect(route('clan', ['id' => $clan->id]));
    }
}
