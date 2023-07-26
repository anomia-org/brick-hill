<?php

namespace App\Http\Requests\Admin\Grant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GrantMembership extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->can('grant membership');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'membership_minutes' => 'required|numeric|min:1|max:2147483647',
            'membership_type' => [
                'required',
                Rule::in([3, 4])
            ],
            'modify_membership' => 'boolean'
        ];
    }
}
