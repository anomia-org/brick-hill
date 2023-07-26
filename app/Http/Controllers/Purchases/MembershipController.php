<?php

namespace App\Http\Controllers\Purchases;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\Models\Membership\{
    BillingProduct,
    Membership,
    LotteryWinner,
    Payment
};

class MembershipController extends Controller
{
    public function index()
    {
        return view('pages.membership.membership');
    }

    public function products()
    {
        $saleDate = Carbon::parse('2022-12-31 23:59:59');
        $sale = 0;
        if (!$saleDate->isPast()) {
            $sale = 0.25;
        }

        $membershipItemPeriods = Payment::getActiveMonthlyItems();

        $bucksData = [];

        try {
            $membershipItems = $membershipItemPeriods[0];
            $bucksData = [
                'id' => $membershipItems['id'],
                'price' => array_values($membershipItems['prizes'])[0],
                'image' => mix("images/extracted/" . $membershipItems['image'])->__toString(),
                'ends_at' => Carbon::parse($membershipItems['endDate'])->format("F jS")
            ];
        } catch (\Exception $e) {
        }

        return [
            'sale' => $sale,
            'sale_ends_at' => $saleDate,
            'products' => BillingProduct::all(),
            'bucks' => $bucksData
        ];
    }

    public function lottery()
    {
        $nextLottery = Carbon::now()->endOfMonth()->setTime(22, 0, 0);
        if ($nextLottery->isPast())
            $nextLottery = Carbon::now()->addWeek()->endOfMonth()->setTime(22, 0, 0);

        return view('pages.membership.lottery')->with([
            'pool' => Membership::active()->sum(DB::raw("IF(membership = 3, 15, IF(membership = 4, 20, 10))")),
            'previous_winners' => LotteryWinner::where('created_at', '>=', Carbon::now()->subDays(3))->get(),
            'next_lottery' => $nextLottery
        ]);
    }
}
