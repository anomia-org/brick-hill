<?php

namespace Database\Factories\Set;

use App\Models\Set\Set;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Set::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'uid' => '',
            'host_key' => Str::random(64),
            'is_dedicated' => true
        ];
    }
}
