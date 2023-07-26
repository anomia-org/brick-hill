<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Preview extends FormRequest
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
            'texture' => 'mimes:jpeg,jpg,png|dimensions:min_width=128,min_height=128,max_width=1024,max_height=1024,ratio=1',
            'mesh' => 'file',
            'type' => [
                'required',
                Rule::in(['tshirt', 'shirt', 'pants', 'hat', 'face', 'tool', 'head'])
            ],
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
            return in_array($input->type, ['tshirt', 'shirt', 'pants', 'hat', 'face', 'tool']);
        });

        $validator->sometimes('mesh', 'required', function($input) {
            return in_array($input->type, ['hat', 'tool', 'head']);
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
