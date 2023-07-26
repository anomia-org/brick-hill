<?php

namespace App\Http\Controllers\User\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{
    Auth,
    Hash,
    Cache
};
use Illuminate\Foundation\Auth\{
    ThrottlesLogins,
    AuthenticatesUsers
};

use Google2FA;

use App\Models\User\{
    User,
    IPAddress
};

use App\Http\Requests\User\Authentication\{
    Login,
    LogoutOtherDevices
};

class LoginController extends Controller
{
    use AuthenticatesUsers, ThrottlesLogins;

    public function loginPage()
    {
        return view('pages.auth.login');
    }

    public function login(Login $request)
    {
        Google2FA::logout();

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        $userData = $request->only('username', 'password');

        $user = User::where('username', $request->username)->where('password', '!=', '')->first();

        if (Auth::attempt($userData, true)) {
            $this->clearLoginAttempts($request);
            if (Hash::needsRehash($user->password)) {
                $user->password = Hash::make($request->password);
                $user->save();
            }
            if ($user->ips()->ip($request->ip())->count() == 0) {
                /*if($email = auth()->user()->email()->verified()->first()) {
                    Mail::to($email->send_email)->send(new \App\Mail\NewLogin($user, ['time' => \Carbon\Carbon::now(), 'ip' => request()->ip()]));
                }*/
                $ip = IPAddress::create([
                    'user_id' => $user->id,
                    'ip' => $request->ip()
                ]);
            }

            return redirect()->intended();
        } else {
            $this->incrementLoginAttempts($request);
            $throttleKey = $this->throttleKey($request);

            if ($this->limiter()->attempts($throttleKey) == '5') {
                if (!Cache::has("{$throttleKey}_mins"))
                    Cache::put("{$throttleKey}_mins", 2, now()->addMinutes(30));
                else
                    Cache::increment("{$throttleKey}_mins");
            }
            return redirect('login')
                ->header('X-Log-Metadata', "AttemptUsername-$request->username")
                ->withErrors(['error' => ['Incorrect username or password']]);
        }
    }

    public function logout()
    {
        Google2FA::logout();
        Auth::logout();

        request()->session()->flush();

        return redirect('/');
    }

    public function logoutOtherDevices(LogoutOtherDevices $request)
    {
        Auth::logoutOtherDevices($request->current_password);

        return success('Logged out of other devices');
    }

    public function decayMinutes()
    {
        return 5 * Cache::get("{$this->throttleKey(request())}_mins", 1);
    }
}
