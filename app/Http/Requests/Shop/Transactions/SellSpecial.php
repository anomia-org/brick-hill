<?php

namespace App\Http\Requests\Shop\Transactions;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SellSpecial extends FormRequest
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
            'item_id' => [
                'required',
                'integer',
                Rule::exists('items', 'id')->where(function ($q) {
                    $q->where('special', true)->orWhere('special_edition', true);
                }),
            ],
            'crate_id' => [
                'required',
                'integer',
                Rule::exists('crate', 'id')->where(function ($q) {
                    $q->where([['user_id', $this->user()->id], ['own', true]]);
                }),
            ],
            'bucks_amount' => 'required|integer|min:1|max:2147483647'
        ];
    }
}
