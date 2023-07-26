<?php

namespace App\Http\Controllers\Game;

use App\Exceptions\Custom\APIException;
use App\Http\Controllers\Controller;

use App\Models\Set\{
    Set,
    SetRating
};
use App\Http\Requests\Game\ChangeRating;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    use \App\Traits\Controllers\PostLimit;

    /**
     * Changes a users rating on a given set, while detecting if it should count the rating
     * 
     * @param \App\Http\Requests\Game\ChangeRating $request 
     * @param \App\Models\Set\Set $set 
     * @return true[] 
     */
    public function changeRating(ChangeRating $request, Set $set)
    {
        if (!$this->canMakeNewPost($set->setRatings()->where('user_id', Auth::id()), 0))
            throw new APIException();

        $hasPlayed = Auth::user()->playedSets()->where('set_id', $set->id)->exists();

        SetRating::updateOrCreate([
            'user_id' => Auth::id(),
            'set_id' => $set->id
        ], [
            'is_liked' => $request->rating,
            'is_active' => !$request->disabled,
            'is_valid' => $hasPlayed,
        ]);

        return ['success' => true];
    }
}
