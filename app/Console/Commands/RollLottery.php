<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Models\Membership\{
    Membership,
    LotteryWinner
};

class RollLottery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lottery:roll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rolls the monthly lottery';

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
        $winningAmount = floor(Membership::active()->sum(DB::raw("IF(membership = 3, 15, IF(membership = 4, 20, 10))")) / 4);
        $winners = Membership::active()->whereDoesntHave('user.bans', function ($q) {
            $q->where([['active', 1], ['length', '>=', 600000]]);
        })->orderByRaw('-LOG(1-RAND())/ IF(membership = 4, 2, 1)')->limit(4)->get();

        foreach ($winners as $winner) {
            $user = $winner->user;

            $user->increment('bucks', $winningAmount);
            LotteryWinner::create([
                'user_id' => $user->id,
                'amount_won' => $winningAmount
            ]);
        }

        return Command::SUCCESS;
    }
}
