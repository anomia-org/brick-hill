<?php

namespace App\Http\Requests\Shop\Transactions;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Item\BuyRequest;

class CancelBuyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $buy_request = BuyRequest::find($this->id);

        return $buy_request && $buy_request->user_id == $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer|exists:buy_requests'
        ];
    }
}
