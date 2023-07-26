<?php

namespace App\Rules\Validation;

use Illuminate\Contracts\Validation\Rule;

use Carbon\Carbon;

use App\Models\User\Email\{
    InvalidEmail,
    EmailBlacklist
};

class ValidEmail implements Rule
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
        $hasEmailBounced = InvalidEmail::email($value)->exists();

        $emailDomain = substr($value, strpos($value, '@') + 1);
        $isDomainBlacklisted = EmailBlacklist::domain($emailDomain)->exists();

        return !($hasEmailBounced || $isDomainBlacklisted);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Email is invalid';
    }
}
