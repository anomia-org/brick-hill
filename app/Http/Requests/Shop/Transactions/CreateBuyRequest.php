<?php

namespace App\Http\Requests\Shop\Transactions;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

use App\Rules\Currency\Bucks;

class CreateBuyRequest extends FormRequest
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
                Rule::exists('items', 'id')->where(function ($q) {
                    $q->where('special', true)->orWhere('special_edition', true);
                }),
            ],
            'bucks_amount' => ['required', 'integer', 'min:1', 'max:2147483647', new Bucks]
        ];
    }
}
