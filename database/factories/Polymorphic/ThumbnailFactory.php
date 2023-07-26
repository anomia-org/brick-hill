<?php

namespace Database\Factories\Polymorphic;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Polymorphic\Thumbnail;

use Carbon\Carbon;

class ThumbnailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Thumbnail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => 'f314c8fa-7f17-5568-8e1b-598dd9099ebc',
            'contents_uuid' => 'f0e256da-9f8e-4176-b636-01562b50b4ce',
            'expires_at' => Carbon::now()->addYear(),
        ];
    }
}
