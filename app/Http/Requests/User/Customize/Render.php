<?php

namespace App\Http\Requests\User\Customize;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Render extends FormRequest
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
            'rebase' => 'boolean',
            'colors' => 'array:head,left_arm,right_arm,torso,left_leg,right_leg',
            'colors.*' => ['required', 'string', 'regex:/^([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'instructions' => [
                'array',
                function ($attribute, $value, $fail) {
                    $allKeys = [];
                    foreach ($value as $key => $instruction) {
                        if (!array_key_exists('type', $instruction) || !array_key_exists('value', $instruction)) {
                            return $fail('Instruction must have both a value and a type');
                        }

                        if ($instruction['type'] == "wearOutfit") {
                            if ($key !== 0) {
                                // no point in processing that extra data before it since it replaces it anyway
                                return $fail('wearOutfit instruction must be first');
                            }
                            $userOwnsOutfit = Auth::user()
                                ->outfits()
                                ->active()
                                ->where('id', $instruction['value'])
                                ->exists();

                            if (!$userOwnsOutfit) {
                                return $fail("You must own the outfit attempting to be worn");
                            }
                        }
                        if ($instruction['type'] == "wear") {
                            array_push($allKeys, $instruction['value']);
                        }
                    }
                    $allKeys = array_unique($allKeys);
                    if (
                        Auth::user()->crate()
                        ->whereIn('crateable_id', $allKeys)
                        ->where('crateable_type', 1)
                        // we only want one of each or else owning multiple could mess up the count
                        ->distinct('crateable_id', 'crateable_type')
                        ->count() < count($allKeys)
                    ) {
                        return $fail('You must own items attempting to be worn');
                    }
                }
            ],
            'instructions.*' => 'array',
            'instructions.*.type' => ['required', 'string', Rule::in(['wear', 'remove', 'wearOutfit', 'reorderClothing'])],
            'instructions.*.value' => ['required']
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
            'colors.*.regex' => 'Color format is incorrect.',
        ];
    }
}
