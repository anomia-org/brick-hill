<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Assets\Uploader;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{
    Auth,
    Cache,
    Storage
};

use App\Models\User\{
    User,
    Crate,
    Admin\Report,
};
use App\Models\Item\Item;
use App\Models\Economy\Purchase;

use App\Http\Requests\General\Toggle;
use App\Models\Event\ClientAccess;
use App\Models\Polymorphic\Asset;

class AdminController extends Controller
{
    public function index()
    {
        return view('pages.admin.index');
    }

    public function giftToAll()
    {
        if (auth()->id() != 13)
            return 'only fish man allowed';
        APIParams(['gift_id', 'item_ids']);
        $gift = request('gift_id');
        $itemsToGrant = explode(',', request('item_ids'));

        $items = Item::whereIn('id', $itemsToGrant)->get(['name'])->toKey('name')->toArray();
        $gift = Item::where('id', $gift)->get()->toKey('name')[0];
        $imploded = implode(', ', $items);

        $csrf = csrf_token();

        return "<span>Are you sure you want to grant items <span style=\"font-weight:700;font-size:20px;\">{$imploded}</span> to users with item <span style=\"font-weight:700;font-size:20px;\">{$gift}</span>?</span><br><br><br><br><br><form method=\"POST\"><input type=\"hidden\" name=\"_token\" value=\"{$csrf}\"><button type=\"submit\">I AM SURE  ((NO PRESSY UNLESS SUREY))</button></form>";
    }

    public function giftToAllPost()
    {
        if (auth()->id() != 13)
            return 'only fish man allowed';
        APIParams(['gift_id', 'item_ids']);

        $lock = Cache::lock("grant_presents_lock", 30);
        try {
            $lock->block(10);

            $gift = request('gift_id');
            $itemsToGrant = explode(',', request('item_ids'));

            Crate::owned()->itemId($gift)->whereDoesntHave('user.crate', function ($q) use ($itemsToGrant) {
                $q->whereIn('id', $itemsToGrant);
            })->chunkById(100, function ($owners) use ($itemsToGrant) {
                foreach ($owners as $owner) {
                    foreach ($itemsToGrant as $item) {
                        // check to make sure they dont already own
                        // do i really need to check for this
                        // ahhh who cares
                        $owns = Crate::ownedBy($owner->user_id)->itemId($item)->exists();
                        if (!$owns) {
                            // grant them item
                            Crate::create([
                                'user_id' => $owner->user_id,
                                'crateable_id' => $item,
                                'crateable_type' => 1
                            ]);
                        }
                    }
                }
            });

            return 'successfully sucked';
        } finally {
            optional($lock)->release();
        }
    }

    public function seenReport()
    {
        APIParams(['report_id']);
        $id = request('report_id');
        $report = Report::find($id);
        if (!$report)
            return JSONErr('Report could not be found');

        $admin = Auth::user();
        if ($report->user_id !== $admin->id && $report->seen == 0)
            $admin->increment('admin_points', 1);
        $admin->save();

        $report->seen = 1;
        $report->save();

        return JSONSuccess();
    }

    public function pendItems(Toggle $request, Uploader $uploader, Item $item, Asset $asset = null)
    {
        if (!Auth::user()->can('update', $item->creator) && $item->creator->id !== config('site.main_account_id'))
            throw new \App\Exceptions\Custom\InvalidDataException('You do not have permission to update this item');

        if (!$item->is_approved && !$item->is_pending)
            throw new \App\Exceptions\Custom\InvalidDataException('Once an item is declined it can no longer be approved again');

        if ($item->creator_id !== Auth::user()->id && $item->is_pending == 1) {
            Auth::user()->increment('admin_points', 1);
            Auth::user()->save();
        }

        if ($request->toggle) {
            $item->is_approved = true;
            $item->is_pending = false;
            $item->save();

            $item->latestAsset->is_approved = true;
            $item->latestAsset->is_pending = false;
            $item->latestAsset->save();
        } else {
            if (is_null($asset) && !is_null($item->latestAsset)) {
                $contents = Storage::get("/v3/assets/{$item->latestAsset->uuid}");
                // as of now an uploaded item can only contain one object
                // this can be incremented upon when it is necessary
                $object = json_decode($contents)[0];
                $typesToDelete = ['shirt', 'tshirt', 'pants'];
                if (in_array($object->type, $typesToDelete)) {
                    $assetId = str_replace('asset://', '', $object->texture);
                    $asset = Asset::findOrFail($assetId);
                }
            }

            \DB::transaction(function () use ($asset, $item, $uploader) {
                if (!is_null($asset)) {
                    $uploader->delete($item->latestAsset);

                    $asset->is_approved = false;
                    $asset->save();
                }

                $item->is_approved = false;
                $item->is_pending = false;
                $item->name = '[ Content Removed ]';
                $item->description = '[ Content Removed ]';
                $item->save();

                if (!is_null($item->latestAsset)) {
                    $uploader->delete($item->latestAsset);

                    $item->latestAsset->is_approved = false;
                    $item->latestAsset->is_pending = false;
                    $item->latestAsset->save();
                }
            });
        }

        if ($request->wantsJson()) {
            return JSONSuccess();
        } else {
            return back();
        }
    }

    public function pendAsset(Toggle $request, Uploader $uploader, Asset $asset = null)
    {
        if (!Auth::user()->can('update', $asset->creator) && $asset->creator->id !== config('site.main_account_id'))
            throw new \App\Exceptions\Custom\InvalidDataException('You do not have permission to update this item');

        if (!$asset->is_approved && !$asset->is_pending)
            throw new \App\Exceptions\Custom\InvalidDataException('Once an asset is declined it can no longer be approved again');

        if ($asset->creator_id !== Auth::user()->id && $asset->is_pending == 1) {
            Auth::user()->increment('admin_points', 1);
            Auth::user()->save();
        }

        if ($request->toggle) {
            $asset->is_approved = 1;
            $asset->is_pending = 0;
            $asset->save();
        } else {
            $uploader->delete($asset);

            $asset->is_approved = 0;
            $asset->is_pending = 0;
            $asset->save();
        }

        if ($request->wantsJson()) {
            return JSONSuccess();
        } else {
            return back();
        }
    }

    public function grantItem()
    {
        $item = request("item_id");
        $username = request("username");

        $user = User::where("username", $username)->first();

        if (!$user)
            return JSONErr("User does not exist.");

        $user_id = $user->id;

        if (Auth::id() == $user->id && !Auth::user()->can('grant themselves items'))
            return JSONErr("Ask an administrator if you need items granted to you.");

        $checkItem = Item::find($item);

        if (!$checkItem)
            return JSONErr("Item does not exist.");

        $product = $checkItem->product()->firstOrCreate();

        $checkOwns = Crate::ownedBy($user_id)->itemId($item)->exists();

        if ($checkOwns)
            return JSONErr("The user already owns this item.");

        $crate = Crate::create([
            'user_id' => $user_id,
            'crateable_id' => $item,
            'crateable_type' => 1
        ]);

        Purchase::create([
            'user_id' => $user_id,
            'seller_id' => config('site.main_account_id'),
            'product_id' => $product->id,
            'crate_id' => $crate->id,
            'price' => 0,
            'pay_id' => 3
        ]);

        return JSONSuccess("Item granted successfully.");
    }

    public function transferCrate(Request $request)
    {
        $from_user = User::findOrFail($request->from_user);
        $to_user = User::findOrFail($request->to_user);
        $crate = Crate::where('user_id', $from_user->id)->findOrFail($request->crate_id);

        $product = $crate->crateable->product()->firstOrCreate();

        \DB::transaction(function () use ($crate, $to_user, $from_user, $product) {
            $crate->user_id = $to_user->id;
            $crate->save();

            Purchase::create([
                'user_id' => $to_user->id,
                'seller_id' => $from_user->id,
                'product_id' => $product->id,
                'crate_id' => $crate->id,
                'price' => 0,
                'pay_id' => 4
            ]);
        });

        return JSONSuccess('Item transferred');
    }

    public function grantWorkshop(Request $request)
    {
        $user = User::findOrFail($request->grant_user);

        ClientAccess::updateOrCreate(['user_id' => $user->id], [
            'can_debug' => $request->can_debug
        ]);

        return JSONSUCCESS('User granted');
    }

    public function changeCurrency()
    {
        $currency = request("currency");
        $quantity = request("quantity");
        $username = request("username");

        if ($currency !== 'bucks' && $currency !== 'bits')
            return JSONErr("Currency must be either bucks or bits.");

        if ($quantity < 0 || $quantity > 2147483647)
            return JSONErr("Currency must be a positive integer.");

        $user = User::where("username", $username)->first();

        if (!$user)
            return JSONErr("User does not exist.");

        if ($user[$currency] < $quantity && request('action') == 'deduct')
            return JSONErr("User has insufficient currency. Currency cannot be negative.");

        $user_id = $user->id;

        if (auth()->id() == $user_id)
            return JSONErr("Ask an administrator if you need your currency changed.");

        switch (request('action')) {
            case 'grant':
                $user = User::find($user_id);
                $user->increment($currency, $quantity);

                return JSONSuccess("Currency granted successfully.");
            case 'deduct':
                $user = User::find($user_id);
                $user->decrement($currency, $quantity);

                return JSONSuccess("Currency deducted successfully.");
        }
    }

    public function exchangePoints()
    {
        $points = request("points");

        if ($points < 300 || $points > 2147483647)
            return JSONErr("The minimum exchange amount is 300 points.");

        if ($points * 10 % 15 !== 0)
            return JSONErr("The number of points must be divisible by 1.5 (the rate of exchange).");

        $admin = Auth::user();
        if ($admin->admin_points < $points)
            return JSONErr("You do not have enough points.");

        $admin->increment('bucks', $points / 1.5);
        $admin->decrement('admin_points', $points);
        $admin->save();
        return JSONSuccess("Points exchanged successfully.");
    }
}
