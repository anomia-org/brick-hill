<?php

namespace App\Http\Requests\User\Trade;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Auth;

use App\Rules\Currency\Bucks;
use App\Models\User\Crate;

class SendTrade extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'receiver' => 'required|exists:users,id',
            'asking_items.*' => 'required|integer',
            'asking_items' => [
                'array',
                function ($attribute, $value, $fail) {
                    if (Crate::whereIn('id', $value)->where('user_id', $this->receiver)->owned()->count() !== count($value))
                        return $fail('Receiver is missing item in trade');
                }
            ],
            'asking_bucks' => 'required|numeric|min:0|max:2147483647',
            'giving_items.*' => 'required|integer',
            'giving_items' => [
                'array',
                'min:1', // they must always give at least one item
                function ($attribute, $value, $fail) {
                    if (Crate::whereIn('id', $value)->where('user_id', Auth::id())->owned()->count() !== count($value))
                        return $fail('Sender is missing item in trade');
                }
            ],
            'giving_bucks' => ['required', 'numeric', 'min:0', 'max:2147483647', new Bucks]
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
            'giving_items.min' => 'You must give at least one item',
        ];
    }
}
