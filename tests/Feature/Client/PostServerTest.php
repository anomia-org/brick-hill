<?php

namespace Tests\Feature\Client;

use App\Models\Set\GameToken;
use App\Models\Set\Server;
use App\Models\Set\Set;
use App\Models\User\Ban;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PostServerTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * Test that the Auth tokens and hosting flow works properly
     *
     * @return void
     */
    public function test_hosting_works_properly()
    {
        /** @var User */
        $user = User::factory()
            ->hasEmail()
            ->has(Set::factory())
            ->create();

        $set = $user->sets()->first();

        $tokenResponse = $this->actingAs($user)
            ->getJson($this->addDomain("v1/auth/generateToken?set={$set->id}"));

        $tokenResponse->assertJson(fn (AssertableJson $json) => $json->has('token'));

        $hostResponse = $this->postServer($set, []);
        $hostResponse->assertOk();

        $token = $tokenResponse->json('token');
        $verifyTokenResponse = $this
            ->getJson($this->addDomain("v1/auth/verifyToken?token={$token}&host_key={$set->host_key}"));

        $verifyTokenResponse
            ->assertJson(fn (AssertableJson $json) => $json->has('validator')->etc());

        $this->travel(3)->minutes();
        $validator = $verifyTokenResponse->json('validator');
        $hostResponseWithPlayers = $this->postServer($set, [$validator]);

        $hostResponseWithPlayers->assertOk();

        $this->assertDatabaseHas('sets', [
            'visits' => 1
        ]);

        $this->assertDatabaseHas('servers', [
            'players' => 1
        ]);

        $this->assertDatabaseHas('played_sets', [
            'user_id' => $user->id,
            'set_id' => $set->id
        ]);

        // ensure that attempting again doesnt grant more visits
        $this->travel(3)->minutes();
        $hostResponseWithPlayers = $this->postServer($set, [$validator]);

        $this->assertDatabaseHas('sets', [
            'visits' => 1
        ]);

        $this->assertDatabaseHas('servers', [
            'players' => 1
        ]);

        $invalidateTokenResponse = $this->post($this->addDomain("API/games/invalidateToken?token={$token}", "www"));
        $invalidateTokenResponse->assertOk();

        $this->assertDatabaseMissing('game_tokens', [
            'token' => $token
        ]);

        // ensure that invalid tokens arent counted
        $this->travel(3)->minutes();
        $hostResponseAfterInvalidate = $this->postServer($set, [$validator]);

        $this->assertDatabaseHas('sets', [
            'visits' => 1
        ]);

        $this->assertDatabaseHas('servers', [
            'players' => 0
        ]);

        $hostResponseAfterInvalidate->assertOk();
    }

    /**
     * Test that the API properly returns the banned users
     * 
     * @return void 
     */
    public function test_banned_users_properly_returned()
    {
        /** @var User */
        $user = User::factory()
            ->hasEmail()
            ->has(Set::factory())
            ->create();

        /** @var User */
        $bannedUser = User::factory()
            ->hasEmail()
            ->create();

        $set = $user->sets()->first();

        // first post doesnt do anything
        $hostResponse = $this->postServer($set, []);
        $hostResponse->assertOk();

        $tokenResponse = $this->actingAs($bannedUser)
            ->getJson($this->addDomain("v1/auth/generateToken?set={$set->id}"));

        $tokenResponse->assertJson(fn (AssertableJson $json) => $json->has('token'));

        $token = $tokenResponse->json('token');
        $verifyTokenResponse = $this
            ->getJson($this->addDomain("v1/auth/verifyToken?token={$token}&host_key={$set->host_key}"));

        $verifyTokenResponse
            ->assertJson(fn (AssertableJson $json) => $json->has('validator')->etc());

        $validator = $verifyTokenResponse->json('validator');

        $hostResponseWithPlayer = $this->postServer($set, [$validator]);

        $hostResponseWithPlayer
            ->assertJson(fn (AssertableJson $json) => $json->where('banned_players', [])->etc());

        $ban = Ban::factory()
            ->for($bannedUser)
            ->create();

        $hostResponseWithBannedPlayer = $this->postServer($set, [$validator]);

        $hostResponseWithBannedPlayer
            ->assertJson(fn (AssertableJson $json) => $json->where('banned_players', [$bannedUser->id])->etc());

        $ban->update([
            'active' => 0,
        ]);

        $hostResponseWithUnbannedPlayer = $this->postServer($set, [$validator]);

        $hostResponseWithUnbannedPlayer
            ->assertJson(fn (AssertableJson $json) => $json->where('banned_players', [])->etc());
    }

    /**
     * Time travel and return the postServer API
     * 
     * @param \App\Models\Set\Set $set 
     * @param array $players 
     * @return \Illuminate\Testing\TestResponse 
     */
    private function postServer(Set $set, array $players)
    {
        // postServer only works after 1 minute has passed, use 2 for extra safety
        $this->travel(2)->minutes();
        return $this->post($this->addDomain('v1/games/postServer'), [
            'host_key' => $set->host_key,
            'port' => 42480,
            'players' => $players
        ]);
    }
}
