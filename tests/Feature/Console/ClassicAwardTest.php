<?php

namespace Tests\Feature\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Illuminate\Support\Carbon;

use App\Models\Item\Item;
use App\Models\User\User;

class ClassicAwardTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected int $crownId = 12739;

    /**
     * Test that classic crown is properly granted
     *
     * @return void
     */
    public function test_classic_crown_granted()
    {
        $user = User::factory()->create();
        $item = Item::factory()->for($user, 'creator')->create();

        // id of the crown is hardcoded so basically uhhhhh
        $item->id = $this->crownId; // @phpstan-ignore-line
        $item->save();

        $this->artisan('grant:classic');
        $this->assertFalse($user->crate()->itemId($this->crownId)->owned()->exists());

        // make sure it doesnt grant early
        $this->travel(364)->days();
        $user->last_online = Carbon::now();
        $user->save();
        $this->artisan('grant:classic');
        $this->assertFalse($user->crate()->itemId($this->crownId)->owned()->exists());

        // it didnt grant above so just skip ahead a few days to check
        // at this point they still havent been online so this shouldnt grant either
        $this->travel(7)->days();
        $this->artisan('grant:classic');
        $this->assertFalse($user->crate()->itemId($this->crownId)->owned()->exists());

        // make them online and check if it grants
        $user->last_online = Carbon::now();
        $user->save();
        $this->artisan('grant:classic');
        $this->assertTrue($user->crate()->itemId($this->crownId)->owned()->exists());

        // run it again to make sure it doesnt duplicate
        $this->artisan('grant:classic');
        $this->assertTrue($user->crate()->itemId($this->crownId)->owned()->count() == 1);
    }
}
