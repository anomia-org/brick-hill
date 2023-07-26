<?php

namespace App\Rules\Currency;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class Bucks implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Auth::user()->bucks >= $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You do not have enough bucks.';
    }
}
