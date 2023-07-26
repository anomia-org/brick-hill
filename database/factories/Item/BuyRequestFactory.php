<?php

namespace Database\Factories\Item;

use App\Models\Item\BuyRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuyRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BuyRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'bucks' => $this->faker->randomNumber(2),
            'active' => true
        ];
    }
}
