<?php

namespace Database\Factories\User;

use App\Models\User\Ban;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ban::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'admin_id' => User::first(),
            'note' => 'You have been banned!',
            'content' => 'bad words',
            'length' => 0,
            'active' => 1,
        ];
    }
}
