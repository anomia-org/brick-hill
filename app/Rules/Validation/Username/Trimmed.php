<?php

namespace App\Rules\Validation\Username;

use Illuminate\Contracts\Validation\Rule;

class Trimmed implements Rule
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
        return !(substr($value, -1) == ' ' || substr($value, 0, 1) == ' ');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You cannot include a space at the beginning or end of your username';
    }
}
