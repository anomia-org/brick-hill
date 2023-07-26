<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\{
    Auth,
    Hash,
    Mail
};
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Models\User\{
    User,
    PastUsername,
    Email\Email,
    Email\InvalidEmail,
};

use App\Models\Item\Item;

use App\Helpers\Event;

class EditController extends Controller
{
    public function transferCurrency()
    {
        $user = Auth::user();

        $rules = [
            'amount' => 'required|integer|min:0',
            'type' => [
                'required',
                Rule::in(['to-bits', 'to-bucks'])
            ]
        ];

        $validator = validator(request()->all(), $rules);

        if ($validator->fails())
            return redirect()
                ->route('currency')
                ->withInput()
                ->withErrors($validator->messages());

        $type = request('type');
        $amount = request('amount');
        if ($type == 'to-bits') {
            $from = 'bucks';
            $to = 'bits';
            $rate = $amount * 10;
        } else {
            $from = 'bits';
            $to = 'bucks';
            $rate = $amount / 10;
            if ($amount % 10 != 0)
                return error('Amount must be divisible by 10');
        }

        if ($user[$from] < $amount)
            return error("You do not have enough $from");

        $user[$from] = $user[$from] - $amount;
        $user[$to] = $user[$to] + $rate;
        $user->save();
        return success('Currency successfully exchanged');
    }

    public function settingsPage()
    {
        $email = Email::where('user_id', Auth::id())
            ->orderBy('created_at', 'DESC')
            ->first();
        return view('pages.user.settings')->with([
            'email' => $email
        ]);
    }

    public function updateDescription()
    {
        $description = request('description');

        $rules = [
            'description' => 'max:6000'
        ];

        $validator = validator(request()->all(), $rules);

        if ($validator->fails())
            return redirect()
                ->route('dashboard')
                ->withInput()
                ->withErrors($validator->messages());

        $user = Auth::user();
        $user->description = $description;
        $user->desc_scrubbed = 0;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Blurb updated');
    }

    public function checkUsername()
    {
        $username = request('u');
        if (!$username)
            return [
                'v' => 'No value given'
            ];

        $userValidator = UsernameValidator($username, Auth::id());

        return [
            'v' => $userValidator
        ];
    }

    public function updateSettings(Event $event)
    {
        APIParams(['type']);
        $user = Auth::user();

        switch (request('type')) {
            case 'changeDescription':
                APIParams(['description']);
                $rules = [
                    'description' => 'max:6000'
                ];
                $validator = validator(request()->all(), $rules);
                if ($validator->fails())
                    return JSONErr($validator->messages()->first());
                $user->description = request('description');
                $user->desc_scrubbed = 0;
                $user->save();
                break;
            case 'changeUsername':
                APIParams(['username']);
                $username = request('username');
                $userValidator = UsernameValidator($username, Auth::id());
                if ($userValidator !== true)
                    return JSONErr($userValidator);
                // username validator purposefully removes error for your own username
                // why did i do that????
                // dont want to change it incase its used anywhere else
                if ($username == $user->username)
                    return JSONErr('That is already your username');
                if ($user->bucks < 250)
                    return JSONErr('Not enough bucks');
                $pastName = PastUsername::create([
                    'user_id' => Auth::id(),
                    'old_username' => Auth::user()->username,
                    'new_username' => request('username'),
                    'hidden' => 0
                ]);
                $user->bucks = $user->bucks - 250;
                $user->username = request('username');
                $user->save();
                break;
            case 'changePassword':
                APIParams(['password']);
                $rules = [
                    'password' => 'required|string|min:6|confirmed'
                ];
                $validator = validator(request()->all(), $rules);
                if ($validator->fails())
                    return error($validator->messages()->first());
                $valid = Hash::check(request('current_password'), Auth::user()->password);
                if (!$valid)
                    return error('Incorrect account password');
                if ($email = $user->email()->verified()->first()) {
                    Mail::to($email->send_email)->send(new \App\Mail\Security\PasswordChange($user));
                }
                $user->password = Hash::make(request('password'));
                $user->save();

                return success('Password changed');
            case 'changeEmail':
                if (!request('email'))
                    return error('No email given');
                $rules = [
                    'email' => 'required|email',
                ];
                $validator = validator(request()->all(), $rules);
                if ($validator->fails())
                    return error($validator->messages()->first());
                $valid = Hash::check(request('current_password'), Auth::user()->password);
                if (!$valid) {
                    return error('Incorrect account password');
                }

                $currentEmail = Email::userId($user->id)->verified()->first();
                if ($currentEmail) {
                    if ($currentEmail->send_email == request('email'))
                        return error('Already current email');
                    if (request('current_email') != $currentEmail->send_email)
                        return error('Incorrect current email');
                }

                $emailsRecently = Email::userId($user->id)
                    ->where('created_at', '>=', Carbon::now()->subDay())
                    ->count();
                if ($emailsRecently > 2)
                    return error('You can only change your email twice a day');
                $check = InvalidEmail::find(request('email'));
                if (!$check) {
                    do {
                        $key = str_random(60);
                        $check = Email::where('verify_code', $key)->count();
                    } while ($check > 0);

                    $email = Email::create([
                        'user_id' => $user->id,
                        'email' => request('email'),
                        'verify_code' => $key,
                        'verified' => 0
                    ]);
                } else {
                    return error('Invalid email');
                }

                Mail::to($email->send_email)->send(new \App\Mail\VerificationMail($user->id));

                return success('Check your email to verify the change');
            case 'changeTheme':
                $rules = [
                    'theme' => ['required', Rule::in([1, 2, 3, 4])]
                ];
                $validator = validator(request()->all(), $rules);
                if ($validator->fails())
                    return JSONErr($validator->messages()->first());
                $user->theme = request('theme');
                $user->save();
                break;
            default:
                return JSONErr();
        }

        return [
            'changed' => true,
            'success' => true
        ];
    }

    public function settingsAPI()
    {
        $user = Auth::user();
        $email = optional($user->email);

        return [
            'user' => [
                'username' => $user->username,
                'id' => $user->id,
                'birthday' => $user->birth,
                'gender' => $user->gender,
                'email' => $email->email,
                'email_verified' => $email->verified,
                'description' => $user->description,
                'is_2fa_active' => $user->tfa_active,
                'membership' => $user->membership,
                'membership_expires_on' => Carbon::parse($user->membership->date ?? 0)->addMinutes($user->membership->length ?? 0)->format('M d Y'),
                'subscription_active' => $user->subscription->active ?? false,
                'is_stripe_customer' => $user->stripeCustomer()->exists(),
                'theme' => $user->theme
            ],
            'themes' => [
                1 => 'Default',
                2 => 'Dark',
                //3 => 'Default',
                //4 => 'Dark',
            ]
        ];
    }
}
