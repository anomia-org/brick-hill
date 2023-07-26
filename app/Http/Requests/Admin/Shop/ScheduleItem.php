<?php

namespace App\Http\Requests\Admin\Shop;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ScheduleItem extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->can('manage shop');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'item.name' => 'required|string|min:3|max:50',
            'item.description' => 'max:500',
            'item.type_id' => 'required|integer|exists:item_types,id',
            'item.series_id' => 'nullable|integer|exists:series,id',
            'item.event.id' => 'nullable|integer|exists:events,id',
            'virtual.offsale' => 'boolean',
            'virtual.free' => 'exclude_if:virtual.offsale,true|boolean',
            'item.bits' => 'exclude_if:virtual.offsale,true|exclude_if:virtual.free,true|nullable|integer|min:1|max:2147483647',
            'item.bucks' => 'exclude_if:virtual.offsale,true|exclude_if:virtual.free,true|nullable|integer|min:1|max:2147483647',
            'item.special' => 'boolean',
            'item.special_edition' => 'boolean',
            'item.stock' => 'exclude_unless:item.special_edition,true|integer|min:1|max:2147483647',
            'item.timer' => 'boolean',
            'item.timer_date' => 'exclude_if:item.timer,false|exclude_if:item.timer,0|date|after:now',
            'virtual.scheduled_for' => 'required|date|after:now',
            'virtual.hide_update' => 'required|boolean'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        // dont require the other price to exist if the user sets one of them
        $validator->sometimes(['item.bits', 'item.bucks'], 'required', function ($input) {
            return !($input->item['bits'] > 0 || $input->item['bucks'] > 0);
        });

        // only want it to not be included if timer is set to true
        // there must be a better rule to do this
        $validator->sometimes(['item.timer'], ['prohibited_if:item.special,true', 'prohibited_if:item.special,1'], function ($input) {
            return $input->timer;
        });
    }
}
