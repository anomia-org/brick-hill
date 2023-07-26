<?php

namespace Database\Factories\Item;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item\Version;
use App\Models\Polymorphic\Asset;

class VersionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Version::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'asset_id' => Asset::factory()
        ];
    }
}
