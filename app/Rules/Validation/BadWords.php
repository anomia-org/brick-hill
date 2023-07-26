<?php

namespace App\Rules\Validation;

use Illuminate\Contracts\Validation\Rule;

class BadWords implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $alnumValue = str_replace(array('-', '_', '.', ' '), '', $value);

        $badWords = [
            'fuck',
        ];

        $strToNum = [
            'a' => '4',
            'b' => '6',
            'e' => '3',
            'g' => '9',
            'h' => '4',
            'i' => '1',
            'l' => '1',
            'o' => '0',
            's' => '5',
            't' => '7',
        ];

        foreach ($badWords as $w) {
            // returns error even if it contains punctuation in the middle
            if (stripos($alnumValue, $w) !== false)
                return false;

            // $new_w will have every common leet letters converted to a corresponding leet number, then check it at the end
            $new_w = $w;
            foreach ($strToNum as $str => $num) {
                $new_w = str_replace($str, $num, $new_w);

                // $temporary_w will convert each common leet letter to a corresponding leet number, checking it each one separately
                $temporary_w = str_replace($str, $num, $w);
                if (stripos($alnumValue, $temporary_w) !== false)
                    return false;
            }

            if (stripos($alnumValue, $new_w) !== false)
                return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':Attribute has a restricted word in it';
    }
}
