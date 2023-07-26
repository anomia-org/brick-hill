<?php

namespace Tests\Feature\User\Membership;

use App\Models\Item\Item;
use App\Models\Membership\Payment;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentItemsTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * Test that ensures that the base donator items are granted properly
     *
     * @return void
     */
    public function test_that_donator_items_are_given()
    {
        /** @var User */
        $user = User::factory()
            ->create();

        Item::factory()->newCreator()->state([
            'id' => 136998
        ])->create();

        Payment::factory()->for($user)->create();

        $this->assertFalse($user->crate()->itemId(136998)->exists());

        Payment::factory()->for($user)->state([
            'gross_in_cents' => 2500
        ])->create();

        $this->assertTrue($user->crate()->itemId(136998)->exists());
    }
}
