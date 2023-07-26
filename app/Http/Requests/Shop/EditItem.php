<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class EditItem extends FormRequest
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
            'description' => 'max:500',
            'offsale' => 'boolean',
            'free' => 'exclude_if:offsale,true|boolean',
            'bits' => 'exclude_if:offsale,true|exclude_if:free,true|nullable|integer|min:1|max:2147483647',
            'bucks' => 'exclude_if:offsale,true|exclude_if:free,true|nullable|integer|min:1|max:2147483647',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator) {
        // dont require the other price to exist if the user sets one of them
        $validator->sometimes(['bits', 'bucks'], 'required', function($input) {
            return !($input->bucks > 0 || $input->bits > 0);
        });
    }
}
