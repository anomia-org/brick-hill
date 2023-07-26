<?php

namespace App\Http\Requests\Admin\Super\Promo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewPromo extends FormRequest
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
            'code' => 'required|alpha_num|unique:promo_codes',
            'item' => [
                'required',
                Rule::exists('items', 'id')->where(function($query) {
                    $query->where('creator_id', config('site.main_account_id'));
                })
            ],
            'date' => 'required|date|after:now'
        ];
    }
}
