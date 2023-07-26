<?php

namespace Tests\Feature\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User\User;
use App\Models\Membership\{
    Membership,
    Subscription
};

class MembershipBucksTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * Test that membership bucks are properly granted
     *
     * @return void
     */
    public function test_membership_bucks_grant()
    {
        $user = User::factory()
            ->has(Membership::factory()->oneMonth()->ace())
            ->has(Subscription::factory()->oneMonth())
            ->createOne();

        // make sure they received the signing bonus
        $this->assertTrue(User::find($user->id)->bucks == 101);

        $this->artisan('membership:bucks');

        // make sure they received the 20 daily bucks
        $this->assertTrue(User::find($user->id)->bucks == 121);

        // make sure that the bonus also still works
        $this->travel(1)->years();

        $user->membership->update([
            'date' => now()
        ]);

        $user->subscription->update([
            'expected_bill' => now()
        ]);

        $this->artisan('membership:bucks');

        $this->assertTrue(User::find($user->id)->bucks == 121 + round(20 * (1.02 ** 12)));
    }

    /**
     * Test that old memberships dont add extra to the longevity bonus
     * 
     * @return void 
     */
    public function test_old_memberships_dont_break()
    {
        $user = User::factory()
            ->has(Membership::factory()->oneMonth()->ace())
            ->has(Subscription::factory()->oneMonth())
            ->createOne();

        $this->travel(1)->years();

        $this->assertTrue(User::find($user->id)->membership()->exists());

        // expire the old membership
        $this->artisan('membership:bucks');

        $this->assertFalse(User::find($user->id)->membership()->exists());

        // create new membership for the user
        Membership::factory()->oneMonth()->ace()->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id
            ];
        })->create();

        Subscription::factory()->oneMonth()->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id
            ];
        })->create();

        $this->artisan('membership:bucks');

        // make sure that they received only the normal daily bucks
        $this->assertTrue(User::find($user->id)->bucks == 121);
    }
}
