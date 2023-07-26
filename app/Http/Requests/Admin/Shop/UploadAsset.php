<?php

namespace App\Http\Requests\Admin\Shop;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UploadAsset extends FormRequest
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
            'type' => [
                'required',
                Rule::in(['image', 'mesh'])
            ],
            'texture' => 'mimes:png,jpg,jpeg',
            'mesh' => 'file',
            'lossless' => 'in:true,false'
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
            return $input->type == 'image';
        });

        $validator->sometimes('texture', 'dimensions:min_width=128,min_height=128,max_width=1024,max_height=1024,ratio=1', function($input) {
            return $input->lossless === 'false';
        });

        $validator->sometimes('mesh', 'required', function($input) {
            return $input->type == 'mesh';
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
