<?php

namespace App\Http\Requests\Clan;

use Illuminate\Foundation\Http\FormRequest;

class CreateClan extends FormRequest
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
            'title' => 'required|string|min:1|max:30',
            'tag' => 'required|string|min:2|max:5|alpha_num',
            'description' => 'max:10000',
            'image' => 'mimes:png,jpg,jpeg|required|dimensions:min_width=128,min_height=128,max_width=1024,max_height=1024,ratio=1'
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
            'image.dimensions' => 'Thumbnail must be square with dimensions 128x128 through 1024x1024'
        ];
    }
}
