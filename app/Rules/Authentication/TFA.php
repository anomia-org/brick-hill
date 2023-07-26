<?php

namespace App\Rules\Authentication;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use PragmaRX\Google2FALaravel\Facade as Google2FA;

class TFA implements Rule
{

    private $recoveryCodes;
    private $window;

    /**
     * Create a new rule instance.
     *
     * @param bool $recoveryCodes Allow for recovery codes to pass.
     * @param int $window The window for the code to be valid.
     * @return void
     */
    public function __construct($recoveryCodes = true, $window = 1)
    {
        $this->recoveryCodes = $recoveryCodes;
        $this->window = $window;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $isTokenCorrect = Google2FA::verifyKey(Auth::user()->secret_2fa, $value, $this->window);
        $recoveryCodes = Auth::user()->tfaRecoveryCodes;

        return $isTokenCorrect || ($this->recoveryCodes && $recoveryCodes && in_array($value, $recoveryCodes->codes));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Two Factor Authentication code is incorrect';
    }
}
