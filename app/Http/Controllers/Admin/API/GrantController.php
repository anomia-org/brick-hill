<?php

namespace App\Http\Controllers\Admin\API;

use App\Exceptions\Custom\APIException;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Http\Requests\Admin\Grant\GrantMembership;

use App\Models\Membership\Membership;
use App\Models\User\User;

class GrantController extends Controller
{
    public function grantMembership(GrantMembership $request, User $user)
    {
        // they are already paying for it so why are we changing it? force them to cancel it first
        if ($user->subscription()->active()->exists())
            throw new APIException("Cannot modify membership on user with active subscription. You can cancel a subscription through the Stripe dashboard.");

        if (Auth::id() == $user->id && !Auth::user()->can('grant themselves membership'))
            throw new APIException("Cannot grant yourself membership");

        // we dont want to accidentally change someones membership that they already have so give a confirmation before attempting to change it
        if (!$request->modify_membership && $user->membership) {
            return [
                'success' => false,
                'can_modify_membership' => true,
            ];
        }

        // deactivate their old membership
        // technically we could overwrite their current membership row with the new membership type
        // but rows are cheap and having data on when a user had membership and when it was ended could be useful later
        if ($request->modify_membership && $user->membership) {
            $user->membership()->update([
                'active' => 0
            ]);
        }

        // all confirmations went through and they dont have a subscription so we can just update their membership now
        Membership::updateOrCreate(
            ['user_id' => $user->id, 'membership' => $request->membership_type],
            ['active' => 1, 'length' => $request->membership_minutes, 'date' => Carbon::now()]
        );

        return [
            'success' => true
        ];
    }
}
