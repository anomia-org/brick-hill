<?php

namespace App\Http\Requests\User\Authentication;

use Illuminate\Foundation\Http\FormRequest;

use Carbon\Carbon;

class SetupToken extends FormRequest
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
            && !auth()->user()->tfa_active // doesnt have 2fa
            && session()->has('add_2fa_ts') // completed the getToken
            && !Carbon::createFromTimestamp(session('add_2fa_ts'))->addHour()->isPast(); // done in the past hour
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required'
        ];
    }
}
