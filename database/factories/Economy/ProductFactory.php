<?php

namespace Database\Factories\Economy;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Economy\Product;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'bucks' => 1,
            'bits' => 1,
            'offsale' => 0,
        ];
    }

    /**
     * Indicate that the product has a price too expensive
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function overpriced()
    {
        return $this->state(function () {
            return [
                'bucks' => 123456789,
                'bits' => 123456789,
            ];
        });
    }

    /**
     * Indicate that the product is purchasable with only bucks
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function onlyBucks()
    {
        return $this->state(function () {
            return [
                'bucks' => 1,
                'bits' => null
            ];
        });
    }

    /**
     * Indicate that the product is purchasable with only bits
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function onlyBits()
    {
        return $this->state(function () {
            return [
                'bucks' => null,
                'bits' => 1
            ];
        });
    }

    /**
     * Indicate that the product is free
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function free()
    {
        return $this->state(function () {
            return [
                'bucks' => 0,
                'bits' => 0
            ];
        });
    }

    /**
     * Indicate that the product is offsale
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function offsale()
    {
        return $this->state(function () {
            return [
                'offsale' => 1,
            ];
        });
    }
}
