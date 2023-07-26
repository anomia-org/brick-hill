<?php

namespace App\Http\Controllers\API\Shop;

use App\Exceptions\Custom\APIException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Item\ItemSpecialResource;

use App\Models\Item\Item;

use App\Http\Resources\Item\ResellerResource;
use App\Models\Economy\Purchase;
use Carbon\Carbon;

class SpecialAPIController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->item)
                return $next($request);

            if (!$request->item->is_special)
                throw new APIException("Item must be special");

            return $next($request);
        });
    }

    /**
     * Return special only data like hoards, amount of buy requests
     * 
     * @param \App\Models\Item\Item $item 
     * @return \App\Http\Resources\Item\ItemSpecialResource 
     */
    public function specialDatav1(Item $item)
    {
        return new ItemSpecialResource($item);
    }

    /**
     * Return counts of active Buy Requests
     * 
     * @param \App\Models\Item\Item $item 
     * @return mixed 
     */
    public function ordersv1(Item $item)
    {
        $orders = $item
            ->buyRequests()
            ->orderBy('bucks', 'DESC')
            ->groupBy('bucks')
            ->selectRaw('bucks, count(*) as count')
            ->get();

        if (count($orders) > 5) {
            $firstFour = $orders->slice(0, 4);
            $fifthCount = $orders->slice(4)
                ->map(function ($buyRequest) {
                    return $buyRequest->count; // @phpstan-ignore-line
                })->sum();

            // @phpstan-ignore-next-line
            $orders = $firstFour->push([
                'bucks' => $orders[4]->bucks,
                'count' => $fifthCount
            ]);
        }

        return $orders;
    }

    /**
     * Return related special chart data for an Item
     * 
     * @param \App\Models\Item\Item $item 
     * @return array 
     */
    public function chartv1(Item $item)
    {
        $chart = $item->purchases()
            ->where([
                ['purchases.pay_id', '<=', 2],
                ['seller_id', '!=', config('site.main_account_id')],
                ['purchases.created_at', '>', Carbon::now()->subDays(90)]
            ])
            ->selectRaw('ROUND(AVG(`price`)) AS avg_price, DATE_FORMAT(`purchases`.`created_at`, "%c-%e") AS date, COUNT(*) AS count')
            ->groupByRaw('DATE_FORMAT(`purchases`.`created_at`, "%c-%e"), products.productable_id')
            ->get()
            ->keyBy('date')
            ->map(fn (Purchase $purchase) => [
                'avg' => (int) $purchase->avg_price, // @phpstan-ignore-line
                'count' => (int) $purchase->count, // @phpstan-ignore-line
            ]);
        return [
            'data' => $chart,
        ];
    }

    /**
     * Return Users currently selling the item
     * 
     * @param \App\Models\Item\Item $item 
     * @return \Illuminate\Http\Resources\Json\ResourceCollection<mixed> 
     */
    public function resellersv2(Item $item)
    {
        return ResellerResource::paginateCollection(
            $item->privateSellers()
                ->with('user', 'crate')
                ->paginateByCursor(['bucks' => 'asc', 'crate_id' => 'asc'])
        );
    }
}
