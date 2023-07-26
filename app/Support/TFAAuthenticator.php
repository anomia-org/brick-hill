<?php

namespace App\Support;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Cache;

use PragmaRX\Google2FALaravel\Support\Constants;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TFAAuthenticator extends Authenticator
{
    use ThrottlesLogins;

    protected function canPassWithoutCheckingOTP()
    {
        return
            !$this->isEnabled() ||
            $this->noUserIsAuthenticated() ||
            !$this->isActivated() ||
            $this->twoFactorAuthStillValid() ||
            !$this->getUser()->tfa_active ||
            app()->environment(['testing', 'local']);
    }

    protected function checkOTP()
    {
        if (!$this->inputHasOneTimePassword() || empty($this->getInputOneTimePassword())) {
            return Constants::OTP_EMPTY;
        }

        if ($this->hasTooManyLoginAttempts(request())) {
            return Constants::OTP_EMPTY;
        }

        $isValid = $this->verifyOneTimePassword();

        if ($isValid) {
            $this->clearLoginAttempts(request());

            $this->login();

            if ($this->getUser()->is_admin)
                session(['admin_ip' => request()->ip()]);

            return Constants::OTP_VALID;
        } else {
            // invalid
            $this->incrementLoginAttempts(request());
            $throttleKey = $this->throttleKey(request());

            if ($this->limiter()->attempts($throttleKey) == '5') {
                if (!Cache::has("{$throttleKey}_mins"))
                    Cache::put("{$throttleKey}_mins", 2, now()->addMinutes(30));
                else
                    Cache::increment("{$throttleKey}_mins");
            }
        }

        return Constants::OTP_INVALID;
    }

    protected function verifyOneTimePassword()
    {
        return $this->verifyAndStoreOneTimePassword($this->getOneTimePassword()) || $this->verifyRecoveryCode($this->getOneTimePassword());
    }

    protected function verifyRecoveryCode($code)
    {
        $recoveryCodes = $this->getUser()->tfaRecoveryCodes;
        if (!$recoveryCodes || count($recoveryCodes->codes) == 0)
            return false;

        if (!in_array($code, $recoveryCodes->codes))
            return false;

        // holy fuck why is it so hard to just remove something from an array ??
        $key = array_search($code, $recoveryCodes->codes);
        $tempCodes = $recoveryCodes->codes;
        array_splice($tempCodes, $key, 1);
        $recoveryCodes->codes = $tempCodes;
        $recoveryCodes->save();

        if (count($recoveryCodes->codes) == 0)
            session(['no_recovery_codes' => true]);

        return true;
    }

    protected function throttleKey(Request $request)
    {
        return Str::lower($this->getUser()->username . '|' . $request->ip());
    }

    public function maxAttempts()
    {
        return 5 * 3; // each request attempt calls it 3 times
    }

    public function decayMinutes()
    {
        return 5 * Cache::get("{$this->throttleKey(request())}_mins", 1);
    }
}
