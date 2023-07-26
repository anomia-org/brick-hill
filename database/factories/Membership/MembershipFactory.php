<?php

namespace Database\Factories\Membership;

use App\Models\Membership\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Membership::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => now(),
            'active' => 1
        ];
    }

    /**
     * Indicate that the membership lasts for one month
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function oneMonth()
    {
        return $this->state(function () {
            return [
                'length' => 43800,
            ];
        });
    }

    /**
     * Indicate that the membership is for ace
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function ace()
    {
        return $this->state(function () {
            return [
                'membership' => 3,
            ];
        });
    }

    /**
     * Indicate that the membership is for royal
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function royal()
    {
        return $this->state(function () {
            return [
                'membership' => 4,
            ];
        });
    }
}
