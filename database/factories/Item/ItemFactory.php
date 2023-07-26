<?php

namespace Database\Factories\Item;

use App\Models\Item\Item;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence,
            'type_id' => 1,
            'timer' => 0,
            'is_approved' => 1,
            'is_pending' => 0
        ];
    }

    /**
     * Indicate that the Item should create a new user as the creator
     * 
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function newCreator()
    {
        return $this->state(function () {
            return [
                'creator_id' => User::factory()
            ];
        });
    }

    /**
     * Indicate that the item is special
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function special()
    {
        return $this->state(function () {
            return [
                'special_edition' => 1,
                'special_q' => 1
            ];
        });
    }
}
