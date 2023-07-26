<?php

namespace Database\Factories\Forum;

use App\Models\Forum\ForumThread;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Forum\ForumBoard;

class ForumThreadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ForumThread::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'board_id' => ForumBoard::all()->random()
        ];
    }
}
