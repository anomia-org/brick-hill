<?php

namespace App\Http\Requests\Game\Server;

use Illuminate\Foundation\Http\FormRequest;

class PostServer extends FormRequest
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
            'host_key' => 'required|size:64|exists:sets',
            'port' => 'required|numeric|max:65535',
            'players.*' => 'alpha_num|distinct',
        ];
    }
}
