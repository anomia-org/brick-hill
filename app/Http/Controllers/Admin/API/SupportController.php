<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Http\Requests\Admin\Super\ReplaceEmail;

use App\Models\User\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    /**
     * Replaces a users email with a new one
     * 
     * @param ReplaceEmail $request 
     * @param User $user 
     * @return void 
     */
    public function replaceEmail(ReplaceEmail $request, User $user)
    {
        $email = $user->email;
        // everyone knows salmon will smash the button 50+ times
        if ($email?->send_email == $request->email && $email->verified)
            throw new \App\Exceptions\Custom\APIException('This is already the users email');

        // set all old emails to unverified
        // why is this attribute not plural if there can be multiple ???
        $user->email()->update(['verified' => 0]);
        $user->email()->create([
            'email' => $request->email,
            'verified' => 1
        ]);
    }

    /**
     * Returns if a user has ever had a given email attached to their account
     * 
     * @param Request $request 
     * @param User $user 
     * @return bool[] 
     */
    public function checkEmail(Request $request, User $user)
    {
        $email = $user->emails()->where('email', $request->email)->first();

        return [
            'is_attached' => !is_null($email),
            'is_currently_verified' => $email?->verified
        ];
    }

    /**
     * Take in a User and their verified email, generate a new TFA recovery code and then send it to their email
     * 
     * @param \Illuminate\Http\Request $request 
     * @param \App\Models\User\User $user 
     * @return true[] 
     */
    public function sendTFARecoveryEmail(Request $request, User $user)
    {
        $email = $user->emails()->where('email', $request->email)->first();

        if (strtolower($email?->send_email) !== strtolower($request->email) || !$email->verified)
            throw new \App\Exceptions\Custom\APIException('Incorrect or unverified email address');

        $recoveryCodes = $user->tfaRecoveryCodes;

        if (!$user->tfa_active || !$recoveryCodes || count($recoveryCodes->codes) == 0)
            throw new \App\Exceptions\Custom\APIException('User does not have TFA active');

        // we want to generate a new recovery code and then add it to their recovery codes to send them a new unique code
        $newCode = Str::lower(Str::random(5) . '-' . Str::random(5) . '-' . Str::random(5));

        $tempCodes = $recoveryCodes->codes;
        array_push($tempCodes, $newCode);

        $recoveryCodes->codes = $tempCodes;
        $recoveryCodes->save();

        Mail::to($email->send_email)->queue(new \App\Mail\Security\TwoFactor\RecoveryRequest($user, $newCode));

        return [
            'success' => true
        ];
    }
}
