<?php

namespace App\Http\Requests\User\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class LogoutOtherDevices extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'current_password' => 'required|current_password'
        ];
    }
}
