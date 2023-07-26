<?php

namespace App\Http\Requests\Admin\Super\Promo;

use App\Models\Event\PromoCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MassImport extends FormRequest
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
            'codes.*' => 'required|alpha_num|distinct',
            'codes' => [
                'required',
                'min:1',
                'array',
                // use a closure to check all codes with a single query
                function($attribute, $value, $fail) {
                    if(PromoCode::whereIn('code', $value)->count() !== 0)
                        return $fail('At least one code provided is already in use');
                }
            ],
            'item' => [
                'required',
                Rule::exists('items', 'id')->where(function($query) {
                    $query->where('creator_id', config('site.main_account_id'));
                })
            ],
        ];
    }
}
