<?php

namespace Tests\Feature\Console;

use App\Models\Item\Item;
use App\Models\User\Crate;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class TradeValueTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * Average price of each special generated
     * 
     * @var int
     */
    private int $avgPrice = 5;

    /**
     * Amount of stock to generate each special with
     * 
     * @var int
     */
    private int $specialQ = 3;

    /**
     * Test that the inserted values are correct
     *
     * @return void
     */
    public function test_value_is_calculated_correctly()
    {
        $user = User::factory()->create();

        $items = $this->generateItems($user, $this->specialQ, $this->avgPrice);

        $this->artisan('trade:value-update');

        $this->assertTrue($user->tradeValues()->exists());

        // ensure the value is correct
        $this->assertTrue($user->tradeValues->first()->value === $this->specialQ * $this->avgPrice);

        $this->assertTrue($user->tradeValues->first()->direction === 0);

        // items value has gone up, make sure value and direction does too
        $items->first()->average_price = $this->avgPrice + 5;
        $items->first()->save();

        $this->artisan('trade:value-update');

        $user->refresh();

        $this->assertTrue($user->tradeValues->first()->value === $this->specialQ * ($this->avgPrice + 5));

        // @phpstan-ignore-next-line
        $this->assertTrue($user->tradeValues->first()->direction === 1);

        // items value has gone down, make sure value and direction does too
        $items->first()->average_price = $this->avgPrice;
        $items->first()->save();

        $this->artisan('trade:value-update');

        $user->refresh();

        $this->assertTrue($user->tradeValues->first()->value === $this->specialQ * $this->avgPrice);

        // @phpstan-ignore-next-line
        $this->assertTrue($user->tradeValues->first()->direction === -1);
    }

    /**
     * Test that unowned items dont count toward value
     * 
     * @return void 
     */
    public function test_owned_items_arent_counted()
    {
        $user = User::factory()->create();

        $this->generateItems($user, $this->specialQ, $this->avgPrice);

        $this->artisan('trade:value-update');

        $this->assertTrue($user->tradeValues()->exists());

        // they no longer own the item, make sure value is removed
        $user->crate()->update([
            'own' => 0
        ]);

        $this->artisan('trade:value-update');

        $user->refresh();

        $this->assertTrue($user->tradeValues->first()->value === 0);

        $this->assertTrue($user->tradeValues->first()->direction === -1);
    }

    /**
     * Test that values are only inserted into the database when they actually change
     * 
     * @return void 
     */
    public function test_value_is_only_inserted_when_necessary()
    {
        $user = User::factory()->create();

        $this->artisan('trade:value-update');

        // user owns no special, they shouldnt have a value
        $this->assertFalse($user->tradeValues()->exists());

        $items = $this->generateItems($user, 3, 1);

        $this->artisan('trade:value-update');

        // ensure the value is correct
        $this->assertTrue($user->tradeValues->first()->value === 3);

        $this->assertTrue($user->tradeValues->first()->direction === 0);

        $id = $user->tradeValues->first()->id;

        // ensure that the value isnt reinserted when their value is the same
        $this->artisan('trade:value-update');

        $user->refresh();

        $this->assertTrue($user->tradeValues->first()->id === $id);

        // their total value is now 6, which should round differently and be inserted
        $items->first()->increment('average_price', 1);

        $this->artisan('trade:value-update');

        $user->refresh();

        // @phpstan-ignore-next-line
        $this->assertTrue($user->tradeValues->first()->value === 3 * 2);

        // their total value is now 12, which shouldnt round differently and not be inserted
        $items->first()->increment('average_price', 2);
        $items->first()->save();

        $this->artisan('trade:value-update');

        $user->refresh();

        $this->assertTrue($user->tradeValues->first()->value === 3 * 2);

        // their total value is now 15, which should now be inserted
        $items->first()->increment('average_price', 1);
        $items->first()->save();

        $this->artisan('trade:value-update');

        $user->refresh();

        // @phpstan-ignore-next-line
        $this->assertTrue($user->tradeValues->first()->value === 3 * 5);
    }

    /**
     * Generate items with set values
     * 
     * @param \App\Models\User\User $user 
     * @param int $stock 
     * @param int $price 
     * @return Collection 
     */
    private function generateItems(User $user, int $stock, int $price): Collection
    {
        // @phpstan-ignore-next-line
        $items = Item::factory()
            ->count(1)
            ->for($user, 'creator')
            ->special()
            ->state(function () use ($stock, $price) {
                return [
                    'special_q' => $stock,
                    'average_price' => $price
                ];
            })
            ->hasProduct()
            ->create();

        $items->each(
            function (Item $item) use ($user) {
                Crate::factory()
                    ->count($item->special_q)
                    ->for($user)
                    ->for($item, 'crateable')
                    ->create();
            }
        );

        return $items;
    }
}
