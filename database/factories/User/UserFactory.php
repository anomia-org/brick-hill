<?php

namespace Database\Factories\User;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => $this->faker->unique()->firstName,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'avatar_hash' => 'c23ac0f4-ae05-53ca-b5a5-403bce648032', // brick-luke, shouldnt change
            'description' => $this->faker->paragraph(5),
            'bits' => 10,
            'bucks' => 1,
            'theme' => 2, // dark theme is best
            'birth' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user is a month old
     *
     * @return static
     */
    public function monthOld()
    {
        return $this->state(function () {
            return [
                'created_at' => Carbon::now()->subMonth(),
            ];
        });
    }

    /**
     * Indicate that the user is a balla
     *
     * @return static
     */
    public function rich()
    {
        return $this->state(function () {
            return [
                'bits' => 100000,
                'bucks' => 100000
            ];
        });
    }

    /**
     * Indicate that the user is an admin
     *
     * @return static
     */
    public function admin()
    {
        return $this->state(function () {
            return [
                'power' => 10,
            ];
        })->tfa()->hasTfaRecoveryCodes();
    }

    /**
     * Indicate that the user has 2FA activated
     *
     * @return static
     */
    public function tfa()
    {
        return $this->state(function () {
            return [
                'secret_2fa' => '2222222222222222',
            ];
        });
    }
}
