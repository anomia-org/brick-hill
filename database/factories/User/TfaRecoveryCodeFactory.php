<?php

namespace Database\Factories\User;

use App\Models\User\TfaRecoveryCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class TfaRecoveryCodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TfaRecoveryCode::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'codes' => array_fill(0, 5, 'codes-codes-codes'),
        ];
    }
}
