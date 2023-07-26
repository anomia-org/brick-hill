<?php

namespace App\Rules\Validation\Username;

use Illuminate\Contracts\Validation\Rule;

class SeparatedSpecials implements Rule
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
        return !(strpos($value, '  ') !== false || strpos($value, '..') !== false || strpos($value, '--') !== false || strpos($value, '__') !== false);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Spaces, periods, hyphens and underscores must be separated.';
    }
}
