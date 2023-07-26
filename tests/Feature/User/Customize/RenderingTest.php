<?php

namespace Tests\Feature\User\Customize;

use App\Models\Item\Item;
use App\Models\User\Crate;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RenderingTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * Test that ensures random values wont be added to the avatar data due to validation issues
     *
     * @return void
     */
    public function test_that_validation_is_proper()
    {
        /** @var User */
        $user = User::factory()
            ->has(
                Crate::factory()
                    ->newItem()
            )
            ->create();

        $this
            ->actingAs($user)
            ->get($this->addDomain("customize", "www"));

        $this->assertModelExists($user->avatar);

        $response = $this
            ->actingAs($user)
            ->post($this->addDomain("v1/user/render/process"), [
                'colors' => [
                    'left_a' => 'FFF'
                ]
            ]);

        $response->assertStatus(400);
    }

    /**
     * Verify that only items you own can be worn
     *
     * @return void
     */
    public function test_only_owned_items_can_be_worn()
    {
        /** @var User */
        $user = User::factory()
            ->has(
                Crate::factory()
                    ->newItem()
            )
            ->create();

        $this
            ->actingAs($user)
            ->get($this->addDomain("customize", "www"));

        $this->assertModelExists($user->avatar);

        // bypass ratelimiting
        $this->travel(1)->day();

        $ownedId = $user->crate->first()->crateable_id;

        $this->assertFalse($user->avatar->items_list->contains($ownedId));

        $response = $this
            ->actingAs($user)
            ->post($this->addDomain("v1/user/render/process"), [
                'instructions' => [
                    [
                        'type' => 'wear',
                        'value' => $ownedId
                    ]
                ]
            ]);

        $response->assertStatus(200);

        $this->assertTrue($user->avatar->items_list->contains($ownedId));

        /** @var Item */
        $item = Item::factory()->newCreator()->create();

        $response = $this
            ->actingAs($user)
            ->post($this->addDomain("v1/user/render/process"), [
                'instructions' => [
                    [
                        'type' => 'wear',
                        'value' => $item->id
                    ]
                ]
            ]);

        $response->assertStatus(400);
    }
}
