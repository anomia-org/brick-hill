<?php

namespace App\Console\Commands\Shop;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Item\Item;
use App\Models\Item\HistoricalSpecialData;
use App\Models\User\Crate;
use Carbon\Carbon;

class GenerateSpecialData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:special-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates and caches analytical data for special items, like hoarded volume and active copies';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Item::special()
            ->select('id', 'daily_views')
            ->withCount([
                'crates as active_copies' => function ($query) {
                    $query->owned()->whereHas('user', function ($query) {
                        $query->where('last_online', '>', Carbon::now()->subMonths(3));
                    });
                },
                'crates as unique_owners' => function ($query) {
                    $query->owned()->select(DB::raw('COUNT(DISTINCT(`user_id`))'));
                },
            ])->chunk(10, function ($items) {
                $historicalData = [];

                /** @var Item[] $items */
                foreach ($items as $item) {
                    $key = "shop" . $item->id . "specialData";

                    // i feel like i forgot mysql writing this
                    // how do i implement it into withcount?
                    // it needs to be wrapped in a subquery as it counts a groupby and then sums the query
                    // i couldnt find a possible method to make it one query but that might be the answer
                    $volumeHoarded = $item
                        ->crates()
                        ->selectRaw("count(*) as hoard_count")
                        ->owned()
                        ->groupBy('user_id')
                        ->havingRaw('COUNT(`user_id`) > ?', [1])
                        ->sum('hoard_count');

                    // not necessary but could be nice to know
                    Cache::put($key . 'lastCalculated', now(), now()->addDays(7));
                    Cache::put($key . 'activeCopies', $item->active_copies, now()->addDays(7)); // @phpstan-ignore-line
                    Cache::put($key . 'uniqueOwners', $item->unique_owners, now()->addDays(7)); // @phpstan-ignore-line
                    Cache::put($key . 'volumeHoarded', $volumeHoarded, now()->addDays(7));

                    $viewCount = Cache::get($key . 'viewCount', 0);
                    Cache::forget($key . "viewCount");

                    Item::withoutSyncingToSearch(function () use ($item, $viewCount) {
                        $item->timestamps = false;
                        $item->daily_views = $this->calculateNextDailyViews($item->daily_views, $viewCount);
                        $item->save();
                    });

                    $historicalData[] = [
                        'item_id' => $item->id,
                        'active_copies' => $item->active_copies, // @phpstan-ignore-line
                        'unique_owners' => $item->unique_owners, // @phpstan-ignore-line
                        'views_today' => $viewCount,
                        'avg_daily_views' => $item->daily_views,
                        'avg_price' => $item->average_price,
                        'volume_hoarded' => $item->volume_hoarded ?? 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                HistoricalSpecialData::insert($historicalData);
            });

        return Command::SUCCESS;
    }

    /**
     * Calculate replacement number of views
     * 
     * @param int $current Current number of views item has
     * @param int $new Amount of views received in past day
     * @return int 
     */
    private function calculateNextDailyViews(?int $current, int $new): int
    {
        // if the current is null, meaning this is the first time setting, set it to new
        if (is_null($current)) {
            return $new;
        }

        $val = ($current * 0.9) + ($new * 0.1);
        if ($current > $new) {
            return (int) floor($val);
        }

        return (int) ceil($val);
    }
}
