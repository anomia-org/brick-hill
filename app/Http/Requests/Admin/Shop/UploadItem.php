<?php

namespace App\Http\Requests\Admin\Shop;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UploadItem extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->can('manage shop');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|min:3|max:50',
            'type' => [
                'required',
                Rule::in(['hat', 'tool', 'face', 'head'])
            ],
            'texture' => 'mimes:png,jpg,jpeg|dimensions:min_width=128,min_height=128,max_width=1024,max_height=1024,ratio=1',
            'mesh' => 'file'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator) {
        $validator->sometimes('texture', 'required', function($input) {
            return $input->type != 'head';
        });

        $validator->sometimes('mesh', 'required', function($input) {
            return $input->type != 'face';
        });
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'texture.dimensions' => 'Texture must be square with dimensions 128x128 through 1024x1024'
        ];
    }
}
