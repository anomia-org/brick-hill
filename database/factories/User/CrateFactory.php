<?php

namespace Database\Factories\User;

use App\Models\Item\Item;
use App\Models\User\Crate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CrateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Crate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'own' => 1
        ];
    }

    /**
     * Indicate that the Crate should create a new Item as the crateable
     * 
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function newItem()
    {
        return $this->state(function () {
            return [
                'crateable_id' => Item::factory()->newCreator(),
                'crateable_type' => 1
            ];
        });
    }
}
