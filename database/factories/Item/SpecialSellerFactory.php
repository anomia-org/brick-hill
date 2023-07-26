<?php

namespace Database\Factories\Item;

use App\Models\Item\SpecialSeller;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialSellerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SpecialSeller::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'bucks' => $this->faker->randomNumber(3, true),
            'active' => true
        ];
    }
}
