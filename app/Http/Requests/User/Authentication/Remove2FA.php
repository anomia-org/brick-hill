<?php

namespace App\Http\Requests\User\Authentication;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\Authentication\TFA;

class Remove2FA extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return
            auth()->check() // logged in
            && auth()->user()->tfa_active; // has 2fa
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => ['required', new TFA],
            'current_password' => 'required|current_password'
        ];
    }
}
