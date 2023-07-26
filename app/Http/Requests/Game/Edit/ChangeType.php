<?php

namespace App\Http\Requests\Game\Edit;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class ChangeType extends FormRequest
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
            'server_type' => [
                'required',
                Rule::in(['nh', 'dedicated'])
            ]
        ];
    }
}
