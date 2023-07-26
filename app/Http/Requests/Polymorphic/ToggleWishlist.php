<?php

namespace App\Http\Requests\Polymorphic;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\Validation\PolyType;

class ToggleWishlist extends FormRequest
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
            'wishlistable_id' =>  ['required', new PolyType('polymorphic_type', 'wishlists')],
            'toggle' => 'required|boolean'
        ];
    }
}
