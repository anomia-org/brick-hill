<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use Carbon\Carbon;

use App\Models\Membership\{
    Membership,
    Subscription,
    MembershipValue,
};

class MembershipBucks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membership:bucks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grants daily membership bucks';

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
        $values = MembershipValue::all()->keyBy('id');
        // set all memberships to their correct active status
        Subscription::active()
            // @phpstan-ignore-next-line
            ->where(DB::raw('(expected_bill + INTERVAL 1 DAY)'), '<', Carbon::now())
            ->update(['active' => 0]);

        // where active membership that is past
        // check for subscription that is active and expecting bill within the next day
        // if it doesnt exist remove their membership
        Membership::active()
            // @phpstan-ignore-next-line
            ->where(DB::raw('(date + INTERVAL length MINUTE)'), '<', Carbon::now())
            ->whereDoesntHave('user.subscription', function (Builder $q) {
                $q->where([
                    ['active', true],
                    [DB::raw('(`expected_bill` + INTERVAL 1 DAY)'), '>=', Carbon::now()]
                ]);
            })
            ->update(['active' => 0]);

        // if it does exist let them have membership for one more day
        Membership::active()
            // @phpstan-ignore-next-line
            ->where(DB::raw('(date + INTERVAL length MINUTE)'), '<', Carbon::now())
            ->whereHas('user.subscription', function (Builder $q) {
                $q->where([
                    ['active', true],
                    [DB::raw('(`expected_bill` + INTERVAL 1 DAY)'), '>=', Carbon::now()]
                ]);
            })
            ->update(['active' => 1, 'date' => DB::raw('date + INTERVAL 1 DAY')]);

        $memberships = Membership::active()->with('user.subscription')->get();

        foreach ($memberships as $membership) {
            $bucks = $values[$membership->membership]->daily_bucks;
            // my brain is simply huge
            // make sure subscription isnt null, as it wont exist when admins grant membership
            if ($membership->length < 50000 && !is_null($membership->user->subscription)) {
                $subscription = $membership->user->subscription;
                if ($subscription->active) {
                    $diff = Carbon::parse($subscription->created_at)->diffInMonths(Carbon::now());
                    $bucks = round($bucks * (1.02 ** $diff));
                }
            }
            $membership->user->increment('bucks', $bucks);
        }

        return Command::SUCCESS;
    }
}
