<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Upload extends FormRequest
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
            'title' => 'required|string|min:3|max:50',
            'type' => [
                'required',
                Rule::in(['tshirt', 'shirt', 'pants'])
            ],
            'file' => 'mimes:png,jpg,jpeg|required|dimensions:min_width=128,min_height=128,max_width=1024,max_height=1024,ratio=1'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'file.dimensions' => 'Texture must be square with dimensions 128x128 through 1024x1024'
        ];
    }
}
