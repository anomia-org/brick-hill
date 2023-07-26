<?php

namespace App\Http\Requests\Shop\Transactions;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\Currency\{
    Bits,
    Bucks
};

class PurchaseProduct extends FormRequest
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
        $priceRules = ['required', 'integer'];
        switch($this->purchase_type) {
            case 0:
                array_push($priceRules, new Bucks);
                break;
            case 1:
                array_push($priceRules, new Bits);
                break;
        }
        return [
            'product_id' => 'required|exists:products,id',
            'purchase_type' => 'required|integer|min:0|max:2',
            'expected_price' => $priceRules
        ];
    }
}
