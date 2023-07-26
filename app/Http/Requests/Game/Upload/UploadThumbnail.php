<?php

namespace App\Http\Requests\Game\Upload;

use Illuminate\Foundation\Http\FormRequest;

class UploadThumbnail extends FormRequest
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
            'file' => 'mimes:png,jpg,jpeg|required|dimensions:width=768,height=512'
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
            'file.dimensions' => 'Thumbnail must be 768x512'
        ];
    }
}
