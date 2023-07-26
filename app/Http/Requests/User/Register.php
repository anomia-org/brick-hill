<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

use App\Rules\Authentication\Captcha;

use App\Rules\Validation\{
    BadWords,
    ValidEmail,
    Username\Trimmed,
    Username\Alphanumeric,
    Username\SeparatedSpecials
};

class Register extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'h-captcha-response' => ['required', new Captcha],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:26',
                'unique:users',
                'unique:past_usernames,old_username',
                new Trimmed,
                new BadWords,
                new Alphanumeric,
                new SeparatedSpecials
            ],
            'password' => 'required|string|min:6|confirmed',
            'email' => ['nullable', 'email', new ValidEmail]
        ];
    }
}
