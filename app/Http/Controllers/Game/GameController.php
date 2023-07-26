<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use App\Models\Set\{
    MasterServer,
    Set
};

class GameController extends Controller
{
    public function play()
    {
        return view('pages.sets.play');
    }

    public function setsPage($id)
    {
        $set = Set::findOrFail($id);

        if (Auth::check()) {
            $rating = $set->setRatings()->active()->userId(auth()->id())->first();
        } else {
            $rating = null;
        }

        return view('pages.sets.set')->with([
            'set' => $set,
            'server' => $set->server,
            'master_server' => MasterServer::active()->first(),
            'favorite_count' => $set->favorites()->active()->count(),
            'has_favorited' => Auth::check() && $set->favorites()->active()->userId(Auth::id())->exists(),

            'up_ratings' => $set->setRatings()->active()->valid()->status(true)->count(),
            'down_ratings' => $set->setRatings()->active()->valid()->status(false)->count(),
            'rating' => $rating,
        ]);
    }
}
