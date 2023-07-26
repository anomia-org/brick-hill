<?php

namespace App\Http\Requests\Game\Edit;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class PlayerAccess extends FormRequest
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
            'max_players' => 'required|numeric|min:1|max:15',
            'who_can_join' => [
                'required',
                Rule::in(['everyone', 'friends'])
            ]
        ];
    }
}
