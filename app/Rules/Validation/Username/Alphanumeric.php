<?php

namespace App\Rules\Validation\Username;

use Illuminate\Contracts\Validation\Rule;

class Alphanumeric implements Rule
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
        $alnumValue = str_replace(array('-','_','.',' '), '', $value);

        return !(strlen($value) < 3 || strlen($value) > 26 || $value != ctype_alnum($alnumValue));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Username must be 3-26 alphanumeric characters (including [ , ., -, _]).';
    }
}
