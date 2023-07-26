<?php

namespace App\Http\Controllers\API\Client;

use App\Exceptions\Custom\APIException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Game\Server\{
    PostServer,
    PostServerCrash
};
use Carbon\Carbon;

use App\Models\User\PlayedSet;
use App\Models\Set\{
    MasterServer,
    GameToken,
    Set
};
use Illuminate\Support\Facades\{
    Cache,
    Redis
};

class LegacySetAPIController extends Controller
{
    public function registerManager(Request $request)
    {
        if ($request->secret !== config('site.keys.manager'))
            throw new APIException('Key is invalid');

        $server = MasterServer::firstOrCreate([
            'ip' => $request->ip()
        ]);

        $server->touch();
    }

    public function postServerCrash(PostServerCrash $request)
    {
        $set = Set::where('host_key', $request->host_key)->firstOrFail();

        if (!$set->is_dedicated)
            throw new APIException("Crash reports only allowed on dedicated servers");

        Redis::setex("set:{$set->id}:crash", 86400 /* 1 day */, $request->report);
    }

    public function latestPostServer(PostServer $request)
    {
        $set = Set::where('host_key', $request->host_key)->firstOrFail();
        $set->timestamps = false;

        $lock = Cache::lock("{$request->host_key}_host_lock", 30);

        try {
            // attempt to get the lock for 10 seconds before failing
            $lock->block(10);

            // sets should still be allowed to be up when dedicated as we control those servers
            if (!$set->is_dedicated && $set->creator->bans()->active()->exists())
                throw new APIException('Banned users cannot host a set');

            if (!$set->server()->exists()) {
                $set->server()->create([
                    'ip_address' => $request->ip(),
                    'port' => $request->port,
                    'players' => 0,
                    'last_post' => Carbon::now()
                ]);
            } else {
                // add a rate limit, use 55 seconds to prevent issues with exact timing
                if (!Carbon::parse($set->server->last_post)->addSeconds(55)->isPast())
                    throw new APIException('You can only postServer once every minute');
            }

            if (!$request->filled('players') || is_null($request->players)) {
                $tokens = collect();
            } else {
                $tokens = GameToken::where([
                    ['created_at', '>=', Carbon::now()->subDay()],
                    ['set_id', $set->id]
                ])
                    ->whereIn('validation_token', $request->players)
                    ->with([
                        'user' => fn ($q) => $q
                            ->select(['id'])
                            ->withCount([
                                'playedSets' => fn ($q) => $q
                                    ->where([['set_id', $set->id], ['created_at', '>=', Carbon::now()->subHours(3)]]),
                                'bans' => fn ($q) => $q->where('active', 1)
                            ]),
                        'user.playedSets' => fn ($q) => $q->where('set_id', $set->id)->whereNull('left_at'),
                    ])
                    ->get();
            }

            // update users who left but failed to send auth invalidate
            $currentlyInGame = $tokens->pluck('user.id');
            $set->plays()->whereNull('left_at')->whereNotIn('user_id', $currentlyInGame)->update([
                'left_at' => Carbon::now()
            ]);

            $amountOfNewPlayers = 0;

            $bannedPlayers = [];

            // create played sets for people who just joined
            // TODO: eloquent has no available bulk insert available for models
            // TODO: this leaves the only option to be a simple array, this code can easily break and stop inserting properly
            // TODO: the tests should report if it breaks, but it may also cause something unexpected that isnt covered in the tests
            // TODO: need to look into way to create the model and then read its values back properly -- but this option will also eat memory
            $newPlayedSets = [];
            $dtString = Carbon::now()->toDateTimeString();
            foreach ($tokens as $token) {
                if ($token->user->bans_count > 0) {
                    $bannedPlayers[] = $token->user->id;
                }

                if ($token->user->playedSets->count() == 0) {
                    $newPlayedSets[] = [
                        'user_id' => $token->user->id,
                        'set_id' => $set->id,
                        'created_at' => $dtString,
                        'updated_at' => $dtString,
                    ];

                    // count the number of new users to grant bits and visits for
                    // exclude out ip linked accs and users who have joined multiple times recently
                    //if ($set->creator->linked_accounts->pluck('account.id')->has($token->user->id))
                    //    continue;
                    if ($token->user->played_sets_count > 0)
                        continue;

                    $amountOfNewPlayers += 1;
                }
            }
            PlayedSet::insert($newPlayedSets);

            if (
                $set->server->ip_address != $request->ip() ||
                $set->server->port != $request->port ||
                $set->server->players != $tokens->count() ||
                Carbon::parse($set->server->last_post)->addMinutes(5)->isPast()
            ) {
                $set->update([
                    'playing' => $tokens->count()
                ]);

                $set->server()->update([
                    'ip_address' => $request->ip(),
                    'port' => $request->port,
                    'players' => $tokens->count(),
                    'last_post' => Carbon::now()
                ]);
            }

            $set->increment('visits', $amountOfNewPlayers);
            $set->creator()->increment('bits', $amountOfNewPlayers);
        } finally {
            optional($lock)->release();
        }

        return [
            'set_id' => $set->id,
            'banned_players' => $bannedPlayers
        ];
    }

    public function invalidateToken(Request $request)
    {
        $token = GameToken::findOrFail($request->token);
        $token->delete();

        $token->user->playedSets()->whereNull('left_at')->where([
            'set_id' => $token->set_id
        ])->update([
            'left_at' => Carbon::now()
        ]);
    }

    public function postServer()
    {
        throw new APIException('This API is deprecated. Update to Node-Hill 11. Documentation for the update can be found at https://brickhill.gitlab.io/open-source/node-hill/index.html with instruction steps 4 and 5.');
    }

    public function latest()
    {
        return \Storage::disk('s3')->lastModified('/client/Player.exe') + \Storage::disk('s3')->lastModified('/client/Workshop.exe');
    }
}
