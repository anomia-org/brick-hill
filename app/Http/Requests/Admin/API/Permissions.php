<?php

namespace App\Http\Requests\Admin\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class Permissions extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'permissions' => 'array',
            'permissions.*' => 'string',
            'model_permissions' => 'array',
            'model_permissions.*' => 'array:permission,relation,id',
            'model_permissions.*.permission' => 'string',
            'model_permissions.*.relation' => 'integer|min:1',
            'model_permissions.*.id' => 'integer|min:1',
        ];
    }
}
