<?php

namespace App\Http\Controllers\User\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Google2FA;
use App\Support\TFAAuthenticator;

use App\Http\Requests\User\Authentication\{
    Remove2FA,
    SetupToken,
    LogoutOtherDevices
};

use App\Models\User\TfaRecoveryCode;
use Illuminate\Support\Facades\Mail;

class TwoFactorController extends Controller
{
    public function logout2fa()
    {
        Google2FA::login();

        return redirect('/');
    }

    public function verify2FA()
    {
        /** @var TFAAuthenticator */
        $authenticator = app(TFAAuthenticator::class)->boot(request());

        return $authenticator->isAuthenticated();
    }

    public function getToken()
    {
        if (Auth::user()->tfa_active)
            return JSONErr('TFA already activated on this account');
        $code = Google2FA::generateSecretKey();

        $qrCodeInline = Google2FA::getQRCodeInline(
            'Brick Hill',
            Auth::user()->username,
            $code,
            256,
            \BaconQrCode\Encoder\Encoder::DEFAULT_BYTE_MODE_ECODING
        );

        session(['add_2fa_token' => $code, 'add_2fa_ts' => now()->timestamp]);

        return [
            'svg' => $qrCodeInline,
            'secret' => $code
        ];
    }

    public function setupToken(SetupToken $request)
    {
        $token = session('add_2fa_token');
        $validKey = Google2FA::verifyKey($token, $request->token, 4); // allow window of 4 here because its only checking if they added it properly

        if (!$validKey)
            return JSONErr('Token is invalid');

        session()->forget(['add_2fa_token', 'add_2fa_ts']);
        session()->save();

        Auth::user()->secret_2fa = $token;
        Auth::user()->save();

        /** @var TFAAuthenticator */
        $authenticator = app(TFAAuthenticator::class)->boot(request());
        $authenticator->login();
        session()->save();

        $recoveryCodes = $this->generateRecoveryCodes();

        if ($email = Auth::user()->email()->verified()->first()) {
            Mail::to($email->send_email)->queue(new \App\Mail\Security\TwoFactor\Activated(Auth::user()));
        }

        $save = TfaRecoveryCode::updateOrCreate(
            ['user_id' => auth()->id()],
            ['codes' => $recoveryCodes]
        );

        return [
            'success' => '2FA activated',
            'recovery_codes' => $recoveryCodes
        ];
    }

    public function remove2FA(Remove2FA $request)
    {
        Google2FA::logout();

        if (session('no_recovery_codes'))
            session(['no_recovery_codes' => false]);

        if ($email = Auth::user()->email()->verified()->first()) {
            Mail::to($email->send_email)->queue(new \App\Mail\Security\TwoFactor\Removed(Auth::user()));
        }

        Auth::user()->secret_2fa = NULL;
        Auth::user()->save();

        return success('2FA has been disabled');
    }

    // use LogoutOtherDevices because its the same request
    public function newRecoveryCodes(LogoutOtherDevices $request)
    {
        $recoveryCodes = $this->generateRecoveryCodes();

        if ($email = Auth::user()->email()->verified()->first()) {
            Mail::to($email->send_email)->queue(new \App\Mail\Security\TwoFactor\NewRecoveryCodes(Auth::user()));
        }

        $save = TfaRecoveryCode::updateOrCreate(
            ['user_id' => Auth::id()],
            ['codes' => $recoveryCodes]
        );

        if (session('no_recovery_codes'))
            session(['no_recovery_codes' => false]);

        return [
            'success' => 'codes generated',
            'recovery_codes' => $recoveryCodes
        ];
    }

    protected function generateRecoveryCodes()
    {
        $recovery = [];
        for ($x = 0; $x < 5; $x++) {
            array_push($recovery, Str::lower(Str::random(5) . '-' . Str::random(5) . '-' . Str::random(5)));
        }
        return $recovery;
    }
}
