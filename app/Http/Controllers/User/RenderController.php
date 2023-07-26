<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Models\User\{
    Avatar,
};
use App\Models\Item\Item;

class RenderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function customizePage()
    {
        $avatar = Avatar::find(Auth::id());

        if (!$avatar) {
            $avatar = Avatar::create([
                'user_id' => Auth::id(),
                'items' => [
                    'hats' => [0, 0, 0, 0, 0],
                    'face' => 0,
                    'tool' => 0,
                    'head' => 0,
                    'figure' => 0,
                    'shirt' => 0,
                    'pants' => 0,
                    'tshirt' => 0
                ],
                'variations' => [],
                'colors' => [
                    'head' => 'f3b700',
                    'torso' => 'f3b700',
                    'right_arm' => 'f3b700',
                    'left_arm' => 'f3b700',
                    'right_leg' => 'f3b700',
                    'left_leg' => 'f3b700'
                ]
            ]);
        }
        return view('pages.user.customize')->with([
            'user' => Auth::user()
        ]);
    }
}
