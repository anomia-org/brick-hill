<?php

namespace Database\Factories\Membership;

use Illuminate\Support\Str;

use App\Models\Membership\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'gross_in_cents' => 1000,
            'email' => $this->faker->safeEmail(),
            'receipt' => 'txn' . Str::random(24),
        ];
    }
}
