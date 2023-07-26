<?php

namespace App\Http\Requests\Shop\Transactions;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

use App\Models\Item\SpecialSeller;

class TakeSpecialOffsale extends FormRequest
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
            'crate_id' => [
                'required',
                Rule::exists('crate', 'id')->where(function ($q) {
                    $q->whereExists(function($q) {
                        // make sure the given crate has a special seller from the requesting user that is active
                        $q->from('special_sellers')->whereRaw('crate.id = crate_id')->where([['user_id', auth()->id()], ['active', true]]);
                    });
                }),
            ],
        ];
    }
}
