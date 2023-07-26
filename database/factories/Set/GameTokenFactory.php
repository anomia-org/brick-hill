<?php

namespace Database\Factories\Set;

use App\Models\Set\GameToken;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User\User;

class GameTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GameToken::class;

    /**
     * Incrementing counter to make non-colliding but consistent validation_tokens
     * 
     * @var int
     */
    private static int $counter = 1;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'token' => $this->faker->uuid3(),
            'user_id' => fn () => User::factory(),
            'ip' => '192.168.0.1',
            'validation_token' => self::$counter++,
        ];
    }
}
