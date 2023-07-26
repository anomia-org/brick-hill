<?php

namespace App\Http\Requests\User\Authentication;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\Authentication\Captcha;
use Illuminate\Support\Facades\App;

class Login extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $captcha = [
            'h-captcha-response' => ['required', new Captcha]
        ];

        // we dont want captcha on testing
        if (App::environment(['local', 'testing'])) {
            $captcha = [];
        }

        return [
            ...$captcha,
            'username' => 'required|string|max:26|exists:users,username',
            'password' => 'required|string'
        ];
    }
}
