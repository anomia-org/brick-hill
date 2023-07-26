<?php

namespace App\Rules\Authentication;

use Illuminate\Contracts\Validation\Rule;

use GuzzleHttp\Client;

class Captcha implements Rule
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
        $client = new Client();

        $response = $client->post('https://hcaptcha.com/siteverify', [
            'form_params' => [
                'secret' => config('site.captcha.hcaptcha.secret'),
                'response' => $value
            ]
        ]);

        $body = json_decode((string)$response->getBody());
        return $body->success;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Captcha is incorrect.';
    }
}
