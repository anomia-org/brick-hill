<?php

namespace Database\Factories\User;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Set\Set;
use App\Models\User\PlayedSet;

class PlayedSetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlayedSet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'left_at' => now(),
            'set_id' => Set::all()->random()
        ];
    }
}
