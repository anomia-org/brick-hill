<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use App\Models\Item\Item;
use App\Models\User\{
    User,
    Avatar
};
use App\Models\Set\{
    Set,
    GameToken
};

class SetAPIController extends Controller
{
    public function genToken()
    {
        $set = Set::findOrFail(request('set'));

        if (!auth()->check())
            return [
                'error' => 'Not logged in'
            ];
            
        $user = auth()->user();
        //$user = User::inRandomOrder()->first();

        $lastToken = GameToken::where([
            ['user_id', $user->id],
            ['created_at', '>=', Carbon::now()->subSeconds(10)]
        ]);

        if ($lastToken->count() > 0)
            return [
                'token' => $lastToken->first()->token
            ];

        do {
            $uuid = Str::uuid();
            $check = GameToken::where('token', $uuid)->count();
        } while ($check > 0);

        $token = GameToken::create([
            'token' => $uuid,
            'user_id' => $user->id,
            'ip' => request()->ip(),
            'set_id' => $set->id,
            'validation_token' => Str::random(64)
        ]);

        return [
            'token' => $uuid
        ];
    }

    /**
     * Returns what Set a token is for, used for the manager to know what Set it should attempt to make
     * 
     * @param \Illuminate\Http\Request $request 
     * @return array 
     */
    public function tokenData(Request $request)
    {
        $token = GameToken::where([
            ['token', $request->token],
            ['created_at', '>=', Carbon::now()->subMinutes(5)],
        ])->firstOrFail();

        return [
            'user_id' => $token->user_id,
            'set_id' => $token->set_id
        ];
    }

    /**
     * Returns data about a user given a GameToken, for use in authenticating a user through Node-Hill
     * 
     * @param \Illuminate\Http\Request $request 
     * @return array 
     */
    public function verifyToken(Request $request)
    {
        APIParams(['token']);

        if ($request->has('host_key')) {
            $set = Set::where('host_key', $request->host_key)->firstOrFail()->id;
        } else {
            $set = $request->set;
        }

        $value = $request->token;
        $token = GameToken::where([
            ['token', $value],
            ['created_at', '>=', Carbon::now()->subMinutes(6)],
            ['set_id', $set]
        ])->orderBy('created_at', 'DESC')->first();

        if ($token) {
            return [
                'user' => User::where('id', $token->user_id)->selectRaw('`id`, `username`, IF(power > 9, 1, 0) AS power')->with('membership')->first()->append('is_admin'),
                'validator' => $token->validation_token
            ];
        } else {
            return [
                'error' => 'Invalid token'
            ];
        }
    }

    public function returnAvatar(Request $request)
    {
        // i believe this errors because Collection->items is a property used and findOrFail returns as a Collection for some reason
        /** @var Avatar */
        $avatar = Avatar::findOrFail($request->id);

        return [
            'user_id' => $avatar->user_id,
            'items' => $avatar->items,
            'colors' => $avatar->colors
        ];
    }
}
