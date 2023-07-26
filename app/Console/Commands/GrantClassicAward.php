<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Models\Item\Item;
use App\Models\User\User;

class GrantClassicAward extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grant:classic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grants users the classic award and hat';

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
        $classicHat = Item::findOrFail(12739);
        $users = User::where([
            ['created_at', '<', Carbon::now()->subYear()],
            ['last_online', '>=', Carbon::now()->subDay()],
        ])->whereDoesntHave('crate', function ($q) use ($classicHat) {
            $q->itemId($classicHat->id);
        })->get();
        foreach ($users as $user) {
            $crate = $user->crate()->make();

            $classicHat->crates()->save($crate);
        }

        return Command::SUCCESS;
    }
}
