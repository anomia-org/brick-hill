<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Mail\PasswordReset as MailPasswordReset;

use App\Models\User\{
    User,
    Email\Email,
    Email\PasswordReset
};

class EmailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('passwordPage', 'sendPasswordEmail', 'resetForgotPassword', 'verifyVerification', 'revertEmail');
    }

    public function sendVerification()
    {
        $email = Auth::user()->email()->whereNull('revert_code')->first();
        if ($email && $email->verified == 0 && is_null($email->last_resend)) {
            Mail::to($email->send_email)->queue(new \App\Mail\VerificationMail(Auth::id()));

            $email->last_resend = Carbon::now();
            $email->save();

            request()->session()->forget('denied_email');
        }

        return redirect()
            ->route('dashboard');
    }

    public function cancelVerification()
    {
        $email = Auth::user()->email()->whereNull('revert_code')->first();
        if ($email && $email->verified == 0 && is_null($email->last_resend)) {
            request()->session()->put('denied_email', true);
        }

        return back();
    }

    public function cancelEmailAdd()
    {
        if (!Auth::user()->email()->whereNull('revert_code')->exists()) {
            request()->session()->put('denied_add', true);
        }

        return back();
    }

    public function verifyVerification(Request $request)
    {
        if (!$request->get('key'))
            return redirect()
                ->route('dashboard');

        $email = Email::where([['verified', 0], ['verify_code', $request->get('key')]])->orderBy('updated_at', 'DESC')->first();
        if (!$email)
            return redirect()
                ->route('dashboard');

        if (
            (Auth::check() && Auth::id() != $email->user_id) &&
            (User::find($email->user_id)->ips()->ip(request()->ip())->count() == 0)
        )
            return redirect()
                ->route('dashboard');

        $checkForOthers = Email::verified()->userId($email->user_id);
        if ($checkForOthers->count() > 0) {
            $curEmail = $checkForOthers->first();

            do {
                $key = str_random(60);
                $check = Email::where('revert_code', $key)->count();
            } while ($check > 0);

            $curEmail->revert_code = $key;
            $curEmail->verified = false;
            $curEmail->save();

            Mail::to($curEmail->send_email)->queue(new \App\Mail\VerifyEmailChange($curEmail->user_id, $curEmail, $email));
        }

        $email->verified = true;
        $email->verify_code = NULL;
        $email->save();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Your email has been verified');
    }

    public function revertEmail()
    {
        if (!request('key'))
            return redirect()
                ->route('dashboard');

        $email = Email::where([['verified', 0], ['revert_code', request('key')]])->orderBy('updated_at', 'DESC')->first();
        if (!$email)
            return redirect()
                ->route('dashboard');

        if (Carbon::parse($email->updated_at)->addMonth()->isPast())
            return redirect()
                ->route('dashboard');

        $unverify = Email::where('created_at', '>', Carbon::parse($email->created_at))->userId($email->user_id)->update([
            'verified' => false,
            'revert_code' => NULL,
            'verify_code' => NULL
        ]);

        $alsoUnverify = Email::userId($email->user_id)->update([
            'verified' => false
        ]);

        $email->revert_code = NULL;
        $email->verified = true;
        $email->save();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Your email has been reverted');
    }

    public function passwordPage(Request $request)
    {
        if (!$request->get('key')) {
            return view('pages.auth.resetpass');
        } else {
            $reset = PasswordReset::where('token', $request->get('key'))
                ->first();
            if (!$reset)
                return redirect('login');
            if (Carbon::parse($reset->created_at)->addHour()->isPast()) {
                $reset->delete();
                return redirect('login');
            }
            $emails = Email::verified()->where('email', $reset->email)->get();
            $accounts = [];
            foreach ($emails as $email) {
                $accounts[] = User::find($email->user_id);
            }

            return view('pages.auth.resetpassacc')->with([
                'accounts' => $accounts
            ]);
        }
    }

    public function sendPasswordEmail(Request $request)
    {
        $rules = [
            'email' => 'required|email'
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails())
            return redirect()
                ->route('password')
                ->withInput()
                ->withErrors($validator->messages());

        $email = Email::verified()->where('email', $request->get('email'))->first();

        if (!$email)
            return redirect()
                ->route('password')
                ->with('success', 'If the email is valid you will receive further instructions');

        do {
            $key = str_random(100);
            $check = PasswordReset::where('token', $key)->count();
        } while ($check > 0);

        $newReset = PasswordReset::create([
            'email' => $request->get('email'),
            'token' => $key
        ]);

        Mail::to($request->get('email'))->queue(new MailPasswordReset($key));

        return redirect()
            ->route('password')
            ->with('success', 'If the email is valid you will receive further instructions');
    }

    public function resetForgotPassword(Request $request)
    {
        $id = $request->get('user');
        $user = User::find($id);
        // verify valid user
        if (!$user)
            return redirect('login');

        $passReset = PasswordReset::where('token', $request->get('key'))->first();
        // verify valid password reset
        if (!$passReset)
            return redirect('login');
        // dont want any old resets!!!
        if (Carbon::parse($passReset->created_at)->addHour()->isPast()) {
            $passReset->delete();
            return redirect('login');
        }

        $email = Email::verified()->where([['user_id', $user->id], ['email', $passReset->email]])->first();
        // verify that the user trying to be reset is actually connected to the email on the reset
        if (!$email)
            return redirect('login');

        $rules = [
            'password' => 'required|string|min:6|confirmed'
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails())
            return redirect()
                ->route('password', ['key' => $request->get('key')])
                ->withErrors(['errors' => $validator->messages()->first()]);

        $user->password = Hash::make($request->get('password'));
        $user->save();

        $passReset->delete();

        return redirect('login')
            ->with('success', 'Account password changed');
    }
}
