<?php

namespace Database\Factories\Membership;

use App\Models\Membership\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

use Carbon\Carbon;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sub_profile_id' => 'sub_aaaaaaaaaaaaaa',
            'active' => 1
        ];
    }

    /**
     * Indicate that the subscription lasts for one month
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function oneMonth()
    {
        return $this->state(function () {
            return [
                'expected_bill' => Carbon::now()->addMonth(),
            ];
        });
    }
}
