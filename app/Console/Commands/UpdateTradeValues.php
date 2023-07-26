<?php

namespace App\Console\Commands;

use App\Constants\ValueDirection;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Models\User\{
    TradeValue,
    User
};

use App\Models\Item\Item;

class UpdateTradeValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trade:value-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all users values based on their crate';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::whereHas('crate', function ($q) {
            $q->whereHasMorph('crateable', [Item::class], function ($q) {
                $q->where(function ($q) {
                    $q->where('special', '>=', 1)
                        ->orWhere('special_edition', '>=', 1);
                });
            });
        })
            ->orWhereHas('tradeValues', function ($q) {
                $q->where('value', '>', 0);
            })
            ->leftJoin('crate', function ($join) {
                $join->on('crate.user_id', '=', 'users.id')
                    ->on('crate.own', '=', DB::raw(1));
            })
            ->leftJoin('items', 'items.id', '=', 'crate.crateable_id')
            ->selectRaw('coalesce(sum(`items`.`average_price`), 0) AS `sum`')
            ->selectSub(
                TradeValue::select('value')
                    ->whereRaw('`trade_values`.`user_id` = `users`.`id`')
                    ->orderBy('id', 'desc')
                    ->limit(1),
                'value'
            )
            ->addSelect('users.id')
            ->groupBy('users.id')
            ->havingRaw('ROUND(`sum`, -1) != ROUND(`value`, -1)')
            ->orHavingRaw('count(`value`) = 0')
            ->chunkById(100, function ($users) {
                $inserts = [];
                foreach ($users as $user) {
                    $direction = ValueDirection::NEUTRAL;
                    if ($user->sum > $user->value && $user->value != 0) {
                        $direction = ValueDirection::INCREASING;
                    } elseif ($user->sum < $user->value) {
                        $direction = ValueDirection::DECREASING;
                    }

                    $inserts[] = [
                        'user_id' => $user->id,
                        'value' => $user->sum,
                        'direction' => $direction,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                // use bulk inserting for more efficiency
                TradeValue::insert($inserts);
            }, 'users.id', 'id');

        return Command::SUCCESS;
    }
}
