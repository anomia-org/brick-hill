<?php

namespace App\Http\Requests\Admin\Super;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\Validation\ValidEmail;

class ReplaceEmail extends FormRequest
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
        return [
            'email' => ['email', new ValidEmail]
        ];
    }
}
