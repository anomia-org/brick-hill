<?php

namespace App\Http\Requests\Admin\Super;

use Illuminate\Foundation\Http\FormRequest;

class SaveRoles extends FormRequest
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
            'roles.*.id' => 'required|integer',
            'roles.*.name' => 'required|string|min:3|max:200',
            'roles.*.permissions.*.id' => 'required|exists:permissions,id',
        ];
    }
}
