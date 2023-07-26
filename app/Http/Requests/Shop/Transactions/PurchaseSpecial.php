<?php

namespace App\Http\Requests\Shop\Transactions;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

use App\Rules\Currency\Bucks;

class PurchaseSpecial extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->expected_seller != $this->user()->id;
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
                    // could put more logic in here but it is better to just put it in the controller to allow for more accurate error messages for the user
                    $q->where([['crateable_id', $this->item_id], ['crateable_type', 1], ['own', true]]);
                }),
            ],
            'purchase_type' => 'required|integer|min:0|max:0',
            'expected_price' => ['required', 'integer', 'min:1', 'max:2147483647', new Bucks],
            'expected_seller' => 'required|integer' // no need to check for exists because the crate_id will already check
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
            'crate_id.exists' => 'User no longer owns the item.'
        ];
    }
}
