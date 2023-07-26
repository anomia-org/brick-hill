<?php

namespace Database\Seeders\Item;

use App\Constants\Thumbnails\ThumbnailType;
use App\Models\Economy\Product;
use App\Models\Item\BuyRequest;
use Illuminate\Database\Seeder;

use App\Models\Item\Item;
use App\Models\Item\Series;
use App\Models\Item\SpecialSeller;
use App\Models\Item\Version;
use App\Models\Polymorphic\Thumbnail;
use App\Models\User\Crate;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Sequence;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $series = Series::factory();

        Item::factory()
            ->count(10)
            ->for(User::find(1), 'creator')
            ->for($series)
            ->hasProduct()
            ->has(Version::factory()->count(10))
            ->create();

        Item::factory()
            ->for(User::find(1), 'creator')
            ->has(Product::factory()->onlyBits())
            ->create();

        Item::factory()
            ->for(User::find(1), 'creator')
            ->has(Product::factory()->onlyBucks())
            ->create();

        Item::factory()
            ->for(User::find(1), 'creator')
            ->has(Product::factory()->free())
            ->create();

        Item::factory()
            ->for(User::find(1), 'creator')
            ->create();

        Item::factory()
            ->count(5)
            ->for(User::find(1), 'creator')
            ->special()
            ->hasProduct()
            ->create();

        $items = Item::factory()
            ->count(2)
            ->for(User::find(1), 'creator')
            ->special()
            ->state(function () {
                return [
                    'special_q' => 15,
                ];
            })
            ->has(BuyRequest::factory()->count(20)->state(new Sequence(
                fn () => ['user_id' => User::all()->random()],
            )))
            ->hasProduct()
            ->create();

        $items->each(
            function (Item $item) {
                $user = User::all()->random();
                Crate::factory()
                    ->count($item->special_q)
                    ->for($user)
                    ->for($item, 'crateable')
                    ->has(SpecialSeller::factory()->for($user)->for($item))
                    ->create();
            }
        );

        $thumbnail = Thumbnail::factory()->create();

        Item::all()->each(
            function (Item $item) use ($thumbnail) {
                if ($item->id != 10) {
                    $item->thumbnails()->attach($thumbnail->id, ['thumbnail_type' => ThumbnailType::ITEM_FULL]);
                }
            }
        );
    }
}
