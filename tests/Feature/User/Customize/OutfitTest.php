<?php

namespace Tests\Feature\User\Customize;

use App\Models\User\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
use Tests\TestCase;

class OutfitTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        // TODO: redis is global on local, causing multiple test runs to fail due to a throttle
        // TODO: need to change the site to just use ThrottleRequests on tests
        $this->withoutMiddleware(
            ThrottleRequestsWithRedis::class
        );
    }

    /**
     * Verify that all Outfit modification APIs are working properly
     *
     * @return void
     */
    public function test_all_outfit_apis_work()
    {
        Storage::fake('s3');

        /** @var User */
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get($this->addDomain("customize", "www"));

        $this->assertModelExists($user->avatar);

        $this->travel(1)->day();

        $response = $this
            ->actingAs($user)
            ->post($this->addDomain("v1/user/outfits/create"), [
                'name' => 'testOutfit'
            ]);
        $response->assertStatus(200);
        $this->assertModelExists($user->outfits()->where('name', 'testOutfit')->first());

        $response = $this
            ->actingAs($user)
            ->post($this->addDomain("v1/user/outfits/1/rename"), [
                'name' => 'nextTestOutfit'
            ]);
        $response->assertStatus(200);
        $this->assertModelExists($user->outfits()->where('name', 'nextTestOutfit')->first());

        $response = $this
            ->actingAs($user)
            ->post($this->addDomain("v1/user/outfits/1/change"));
        $response->assertStatus(500);

        $response = $this
            ->actingAs($user)
            ->post($this->addDomain("v1/user/render/process"), [
                'colors' => [
                    'left_arm' => 'fff'
                ]
            ]);

        $response = $this
            ->actingAs($user)
            ->post($this->addDomain("v1/user/outfits/1/change"));
        $response->assertStatus(200);
        $this->assertModelExists($user->outfits()->where('colors->left_arm', 'fff')->first());

        $response = $this
            ->actingAs($user)
            ->post($this->addDomain("v1/user/outfits/1/delete"));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('outfits', [
            'name' => 'nextTestOutfit',
            'active' => 1
        ]);
    }
}
