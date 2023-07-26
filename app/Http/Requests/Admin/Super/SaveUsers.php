<?php

namespace App\Http\Requests\Admin\Super;

use Illuminate\Foundation\Http\FormRequest;

class SaveUsers extends FormRequest
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
            'users.*.id' => 'required|integer|exists:users,id',
            'users.*.power' => 'required|integer|min:1',
            'users.*.roles.*' => 'exists:roles,id',
        ];
    }
}
