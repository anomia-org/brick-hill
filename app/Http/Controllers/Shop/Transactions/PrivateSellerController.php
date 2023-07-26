<?php

namespace App\Http\Controllers\Shop\Transactions;

use App\Exceptions\Custom\APIException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use App\Http\Requests\Shop\Transactions\{
    SellSpecial,
    PurchaseSpecial,
    TakeSpecialOffsale
};

use App\Models\Item\{
    Item,
    SpecialSeller
};
use App\Models\Economy\Purchase;
use App\Models\User\Crate;

class PrivateSellerController extends Controller
{
    public function sellSpecial(SellSpecial $request)
    {
        $item = Item::findOrFail($request->item_id);

        $crate = Crate::ownedBy(Auth::id())->itemId($request->item_id)->findOrFail($request->crate_id);

        $amount = SpecialSeller::userId(Auth::id())->active()->count();
        $max = Auth::user()->membership_limits->specials_onsale;
        if ($max > 0 && $amount >= $max)
            throw new APIException("You may only have $max specials onsale");

        // check to make sure they arent already trying to sell the item
        $alreadySelling = SpecialSeller::crateId($request->crate_id)
            ->itemId($request->item_id)
            ->userId(Auth::id())
            ->active()
            ->exists();

        if ($alreadySelling)
            throw new APIException('You are already selling this item');

        return Cache::lock('special_' . $request->item_id . '_sell_lock', 5)->get(function () use ($request, $item, $crate) {
            // check the highest, oldest buy request that's NOT yours
            $buy_requests = $item->buyRequests()->with('requester')->where([['user_id', '!=', Auth::id()], ['bucks', '>=', $request->bucks_amount]])->order()->get();
            foreach ($buy_requests as $buy_request) {
                // deactivate the request as it'll either get bought, ending the loop, or requester has insufficient bucks so invalid
                $buy_request->active = false;
                $buy_request->save();

                if ($buy_request->requester->bucks >= $buy_request->bucks) {
                    // the request is valid, trade the items
                    \DB::transaction(function () use ($buy_request, $crate, $item) {
                        Purchase::create([
                            'user_id' => $buy_request->user_id,
                            'seller_id' => Auth::id(),
                            'product_id' => $item->product->id,
                            'crate_id' => $crate->id,
                            'price' => $buy_request->bucks,
                            'pay_id' => 0
                        ]);

                        $crate->user_id = $buy_request->user_id;
                        $crate->save();

                        $buy_request->requester()->decrement('bucks', $buy_request->bucks);
                        Auth::user()->increment('bucks', round($buy_request->bucks * Auth::user()->membership_limits->tax_rate));
                    });

                    return [
                        'success' => true
                    ];
                    // loop ends - no further buy requests will be deactivated
                }
                // the requester has insufficient bucks so their request has been deactivated; loop continues
            }

            $buy_request = SpecialSeller::updateOrCreate(
                ['user_id' => Auth::id(), 'item_id' => $crate->crateable_id, 'crate_id' => $crate->id],
                ['bucks' => $request->bucks_amount, 'active' => 1]
            );

            return [
                'success' => true
            ];
        });
    }

    public function purchaseSpecialSeller(PurchaseSpecial $request)
    {
        Cache::lock('special_' . $request->item_id . '_purchase_lock', 5)->get(function () use ($request) {
            $crate = Crate::owned()->itemId($request->item_id)->findOrFail($request->crate_id);

            $seller = SpecialSeller::crateId($request->crate_id)
                ->userId($request->expected_seller)
                ->active()
                ->first();

            if (!$seller)
                throw new APIException('User is no longer selling this item');

            if ($crate->user_id != $request->expected_seller) {
                $seller->active = 0;
                $seller->save();
                throw new APIException('User is no longer selling this item');
            }

            if ($seller->bucks != $request->expected_price)
                throw new APIException('Item is not selling for expected price');

            // make the special seller offsale
            $seller->active = 0;
            $seller->save();

            if (count($seller->getChanges()) == 1)
                throw new APIException('An error occured processing this transaction.');

            \DB::transaction(function () use ($request, $crate, $seller) {
                // change crate owner
                $crate->user_id = Auth::id();
                $crate->save();

                // add to purchases
                Purchase::create([
                    'user_id' => Auth::user()->id,
                    'seller_id' => $request->expected_seller,
                    'product_id' => Item::findOrFail($request->item_id)->product->id,
                    'crate_id' => $crate->id,
                    'price' => $request->expected_price,
                    'pay_id' => 0
                ]);

                // deduct currency
                Auth::user()->decrement('bucks', $seller->bucks);

                // grant currency to the seller
                $seller->creator()->increment('bucks', round($seller->bucks * $seller->creator->membership_limits->tax_rate));
            });

            return [
                'success' => true
            ];
        });
    }

    public function takeSpecialOffsale(TakeSpecialOffsale $request)
    {
        // dont need to even check just run an update query
        SpecialSeller::active()
            ->crateId($request->crate_id)
            ->userId(Auth::id())
            ->update(['active' => 0]);

        // reindex the item
        Crate::findOrFail($request->crate_id)->crateable->searchable();

        return [
            'success' => true
        ];
    }
}
