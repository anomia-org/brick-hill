<?php

namespace Database\Factories\Polymorphic;

use App\Models\Polymorphic\Asset;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Asset::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'asset_type_id' => 3,
            'is_approved' => 1,
            'is_pending' => 0,
            'is_private' => 0,
            'uuid' => 'f0e256da-9f8e-4176-b636-01562b50b4ce',
        ];
    }

    /**
     * Indicate that the Asset should create a new user as the creator
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
}
