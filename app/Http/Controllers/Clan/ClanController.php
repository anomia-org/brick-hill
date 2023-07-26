<?php

namespace App\Http\Controllers\Clan;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Helpers\Helper;
use Carbon\Carbon;

use App\Models\User\User;
use App\Models\Clan\{
    Clan,
    ClanRank,
    ClanMember,
    ClanRelation
};

class ClanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index', 'clans', 'memberAPI', 'relationAPI');
    }

    public function index($page = 1, $search = '')
    {
        $clans = Clan::select(['clans.id', 'clans.title', 'clans.description', 'clans.approved', 'm.clan_id', DB::raw('count(m.id) as total')])
            ->leftJoin('clan_members as m', function ($join) {
                $join->on('m.clan_id', 'clans.id');
            })
            ->where([['m.status', 'accepted'], ['clans.title', 'like', "%$search%"]])
            ->groupBy('clans.id')
            ->orderBy('total', 'DESC')
            ->offset(20 * $page - 20)
            ->limit(20)
            ->with('latestAsset')
            ->get();

        $pageCount = ceil(Clan::where('clans.title', 'like', "%$search%")->count() / 20);

        $clans->each(function ($collect) {
            $collect->append('clan');
        });

        // add helper if over
        //if(Helper::ifOver())
        //	return redirect(route());

        if (Auth::check()) {
            $userClans = ClanMember::where([['user_id', Auth::id()], ['status', 'accepted']])
                ->get();
            $userClans->each(function ($collect) {
                $collect->append('clan');
            });
        } else {
            $userClans = false;
        }

        return view('pages.clans.index')->with([
            'user_clans' => $userClans,
            'clans' => $clans,
            'pages' => \App\Helpers\Helper::paginate(10, $page, 6, $pageCount, '/clans/'),
            'search' => $search
        ]);
    }

    public function joinClan()
    {
        $id = request('clan_id');

        $clan = Clan::find($id);

        if (!$clan)
            return redirect(route('clanView'));

        if (request('type') == 'leave') {
            if ($clan['owner_id'] == Auth::id() && $clan['ownership'] == 'user')
                return redirect(route('clan', ['id' => $id]))
                    ->withErrors(['errors' => 'You must abandon the clan in the settings']);
            $member = ClanMember::where([['clan_id', $id], ['user_id', Auth::id()], ['status', 'accepted']])
                ->first();
            $member->status = 'declined';
            $member->save();
            return redirect(route('clan', ['id' => $id]));
        }

        if (request('type') == 'claim') {
            if ($clan['ownership'] == 'none') {
                $getUserClans = Clan::where([['ownership', 'user'], ['owner_id', Auth::id()]])
                    ->count();
                if ($getUserClans > auth()->user()->membership_limits->create_clans) {
                    return redirect(route('clan', ['id' => $id]))
                        ->withErrors(['errors' => 'You can only own ' . auth()->user()->membership_limits->create_clans . ' ' . str_plural('clan', auth()->user()->membership_limits->create_clans) . '!']);
                }
                $clan->owner_id = Auth::id();
                $clan->ownership = 'user';
                $clan->save();

                $member = ClanMember::where([['clan_id', $id], ['user_id', Auth::id()]])
                    ->first();

                $highestRank = ClanRank::where('clan_id', $id)
                    ->orderBy('rank_id', 'DESC')
                    ->first();

                if ($member) {
                    $member->rank = $highestRank['rank_id'];
                    $member->status = 'accepted';
                    $member->save();
                } else {
                    ClanMember::create([
                        'clan_id' => $id,
                        'user_id' => Auth::id(),
                        'rank' => $highestRank['rank_id'],
                        'status' => 'accepted'
                    ]);
                }
            }
            return redirect(route('clan', ['id' => $id]));
        }

        $memberCount = ClanMember::where([['status', 'accepted'], ['user_id', Auth::id()]])
            ->count();
        if ($memberCount >= auth()->user()->membership_limits->join_clans) {
            return redirect(route('clan', ['id' => $id]))
                ->withErrors(['errors' => 'You can only be in ' . auth()->user()->membership_limits->join_clans . ' ' . str_plural('clan', auth()->user()->membership_limits->join_clans) . '!']);
        }

        $lowestRank = ClanRank::where('clan_id', $id)
            ->orderBy('rank_id', 'ASC')
            ->first();

        $member = ClanMember::where([['clan_id', $id], ['user_id', Auth::id()]])
            ->first();

        if ($clan['type'] == 'open' || $clan['type'] == 'trial') {
            $status = 'accepted';
        } else {
            $status = 'pending';
        }

        if ($member) {
            switch ($member->status) {
                case 'pending':
                case 'accepted':
                    return redirect(route('clan', ['id' => $id]));
                case 'declined':
                    $member->rank = $lowestRank['rank_id'];
                    $member->status = $status;
                    $member->save();
                    break;
            }
            return redirect(route('clan', ['id' => $id]));
        }

        ClanMember::create([
            'clan_id' => $id,
            'user_id' => Auth::id(),
            'rank' => $lowestRank['rank_id'],
            'status' => $status
        ]);

        return redirect(route('clan', ['id' => $id]));
    }

    public function makePrimary()
    {
        $id = request('clan_id');
        $user = Auth::user();

        if ($user->primary_clan_id == $id) {
            $user->primary_clan_id = NULL;
            $user->save();
            return success('Removed as primary');
        }

        $clan = Clan::findOrFail($id);

        $member = ClanMember::where([['clan_id', $id], ['user_id', Auth::id()], ['status', 'accepted']])
            ->first();

        if (!$member)
            return error('Not a member');

        $user->primary_clan_id = $id;
        $user->save();

        return success('Made primary');
    }

    public function clans($id)
    {
        $clan = Clan::find($id);

        if (!$clan)
            return redirect(route('clanView'));

        if ($clan['ownership'] == 'user') {
            $owner = User::find($clan['owner_id']);
        } else if ($clan['ownership'] == 'clan') {
            $owner = Clan::find($clan['owner_id']);
        } else {
            $owner = false;
        }

        $clanRanks = ClanRank::where('clan_id', $id)->orderBy('rank_id', 'ASC')->get();

        $memCount = ClanMember::where([['clan_id', $id], ['status', 'accepted']])->count();
        $isUserInClan = (Auth::check() && ClanMember::where([['clan_id', $id], ['status', 'accepted'], ['user_id', Auth::id()]])->count() > 0);
        $isUserOwner = (Auth::check() && $clan['ownership'] == 'user' && $clan['owner_id'] == Auth::id());
        $clanMember = ClanMember::where([['clan_id', $id], ['user_id', Auth::id()]])->first();
        $allyRelations = ClanRelation::where([['from_clan', $id], ['status', 'accepted'], ['relation', 'ally']])
            ->orWhere([['to_clan', $id], ['status', 'accepted'], ['relation', 'ally']])
            ->limit(6)
            ->get();
        $enemyRelations = ClanRelation::where([['from_clan', $id], ['status', 'accepted'], ['relation', 'enemy']])
            ->orWhere([['to_clan', $id], ['status', 'accepted'], ['relation', 'enemy']])
            ->limit(6)
            ->get();
        if ($clanMember) {
            $clanRank = ClanRank::where([['clan_id', $id], ['rank_id', $clanMember['rank']]])->first();
        } else {
            $clanRank = '';
        }


        return view('pages.clans.clan')->with([
            'clan' => $clan,
            'owner' => $owner,
            'ranks' => $clanRanks,
            'members' => $memCount,
            'allyRelations' => $allyRelations,
            'enemyRelations' => $enemyRelations,
            'isUserInClan' => $isUserInClan,
            'isUserOwner' => $isUserOwner,
            'userMember' => $clanMember,
            'userRank' => $clanRank
        ]);
    }

    public function memberAPI($clan_id, $rank_id, $page = 1, $limit = 10)
    {
        $clan = Clan::find($clan_id);

        if (!$clan)
            return JSONErr('Invalid clan');

        $rank = ClanRank::where([['clan_id', $clan_id], ['rank_id', $rank_id]])->first();

        if (!$rank)
            return JSONErr('Invalid rank');

        if ($limit > 12)
            $limit = 12;

        $totalPages = ceil(ClanMember::where([['clan_id', $clan_id], ['rank', $rank_id], ['status', 'accepted']])->count() / $limit);

        $members = ClanMember::where([['clan_id', $clan_id], ['rank', $rank_id], ['status', 'accepted']])
            ->orderBy('updated_at', 'DESC')
            ->offset(($page * $limit) - $limit)
            ->take($limit)
            ->with('user')
            ->get();

        return response()->json(['data' => $members, 'pages' => \App\Helpers\Helper::paginate($limit, $page, 8, $totalPages)]);
    }

    public function relationAPI($clan_id, $search, $page = 1)
    {
        $clan = Clan::find($clan_id);

        if (!$clan)
            return JSONErr('Invalid clan');

        $clans = Clan::where([['id', '!=', $clan_id], ['title', 'like', "%$search%"]])
            ->with('latestAsset')
            ->limit(10)
            ->get(['id', 'title'])->each->append('thumbnail');

        return [
            'data' => $clans
        ];
    }
}
