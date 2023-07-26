<?php

namespace App\Http\Controllers\Shop\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use App\Http\Requests\Shop\Transactions\PurchaseProduct;

use App\Models\Economy\{
    Product,
    Purchase
};
use App\Models\User\Crate;


class ProductController extends Controller
{
    public function purchaseProduct(PurchaseProduct $request)
    {
        $payment = $request->purchase_type;
        $payment_type = ($payment == 0) ? 'bucks' : 'bits';

        $product = Product::with('productable')->findOrFail($request->product_id);

        $item_price = $product[$payment_type];

        if ($payment == 2) {
            if ($product->bits === 0 || $product->bucks === 0) {
                $item_price = 0;
            } else {
                $request->session()->flash('error', 'Product is not selling for expected price');
                return;
            }
        }

        if ($item_price !== (int) $request->expected_price) {
            $request->session()->flash('error', 'Product is not selling for expected price');
            return;
        }

        // @phpstan-ignore-next-line
        if (is_null($item_price) || $item_price < 0 || $product->offsale) {
            $request->session()->flash('error', 'Product is not for sale');
            return;
        }

        // return if not free when purchasing with free item type
        if ($request->purchase_type == 2 && ($product->bits !== 0 && $product->bucks !== 0)) {
            $request->session()->flash('error', 'Invalid purchase type');
            return;
        }

        // one lock for each item
        // could potentially create long waiting times to purchasing high volume items
        // will see when that happens
        $lock = Cache::lock('product_' . $product->id . '_purchase_lock', 5);
        try {
            // Attempt aquiring the lock for 5 seconds
            $lock->block(5);

            if (!$product->productable->isPurchasable()) {
                $request->session()->flash('error', 'Product can no longer be purchased');
                return;
            }

            // check to see if already owned
            $ownCheck = Crate::ownedBy(Auth::id())->itemId($product->productable_id)
                ->count();

            if ($ownCheck > 0) {
                $request->session()->flash('error', 'You already own this product');
                return;
            }

            \DB::transaction(function () use ($request, $product, $payment_type) {
                // grant item
                $crate = Auth::user()->crate()->make();
                $product->productable->crates()->save($crate);

                // add to purchases
                Purchase::create([
                    'user_id' => Auth::id(),
                    'seller_id' => $product->productable->creator_id,
                    'product_id' => $request->product_id,
                    'crate_id' => $crate->id,
                    'price' => $request->expected_price,
                    'pay_id' => $request->purchase_type
                ]);

                // deduct currency
                Auth::user()->decrement($payment_type, $request->expected_price);

                // grant currency to the seller
                $product->productable->creator()->increment($payment_type, round($request->expected_price * $product->productable->creator->membership_limits->tax_rate));
            });

            if ($request->expected_price == 0 && $payment_type == 'bits') {
                $request->session()->flash('success', "{$product->productable->name} has been successfully purchased for free!");
                return;
            }
            $request->session()->flash('success', "{$product->productable->name} has been successfully purchased for $request->expected_price " . str_plural($payment_type, $request->expected_price));
            return;
        } catch (LockTimeoutException $e) {
            $request->session()->flash('error', 'Purchase queue full. Try again soon.');
            return;
        } finally {
            optional($lock)->release();
        }
    }
}
