<?php

namespace App\Http\Controllers\API\Events;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\Exceptions\Custom\APIException;
use App\Http\Requests\Event\RedeemPromo;
use App\Http\Resources\Event\PromoCodeResource;
use App\Models\Event\PromoCode;
use App\Models\User\Crate;

class PromoController extends Controller
{
    /**
     * Redeems a PromoCode on the authenticated user
     * 
     * @param RedeemPromo $request 
     * @return true[] 
     */
    public function redeemPromo(RedeemPromo $request)
    {
        $lock = Cache::lock("{$request->code}_promo_lock", 30);

        // TODO: unit test
        try {
            // attempt to get the lock for 10 seconds before failing
            $lock->block(10);

            $code = PromoCode::notExpired()->code($request->code)->firstOrFail();

            DB::transaction(function () use ($code) {
                $alreadyOwned = Crate::ownedBy(Auth::id())
                    ->itemId($code->item_id)->exists();
                if ($alreadyOwned)
                    throw new APIException("You cannot redeem a code for an item you already own");

                $crate = Auth::user()->crate()->make();

                $code->item->crates()->save($crate);

                $product = $code->item->product()->firstOrCreate();

                Auth::user()->purchases()->create([
                    'seller_id' => config('site.main_account_id'),
                    'product_id' => $product->id,
                    'crate_id' => $crate->id,
                    'price' => 0,
                    'pay_id' => 5
                ]);

                if ($code->is_single_use) {
                    $code->is_redeemed = 1;
                    $code->redeemed_by = Auth::id();
                    $code->save();
                }
            });
        } finally {
            optional($lock)->release();
        }

        return [
            'success' => true
        ];
    }

    /**
     * Return current active promos for display
     * 
     * @return AnonymousResourceCollection 
     */
    public function activePromos()
    {
        return PromoCodeResource::collection(
            PromoCode::notExpired()
                // if we sort the other way the promocode lineup will get filled with limited time items that arent redeemed but no longer in use
                ->orderBy('item_id', 'DESC')
                ->groupBy('item_id')
                ->selectRaw('item_id, MAX(expires_at) as expires_at')
                ->limit(5)
                ->with('item')
                ->get()
        );
    }
}
