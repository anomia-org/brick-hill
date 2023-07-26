<?php

namespace App\Http\Controllers\Shop;

use App\Constants\Thumbnails\ThumbnailType;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

use Carbon\Carbon;

use App\Models\Item\Item;

class ItemController extends Controller
{
    public function itemPage(Item $item)
    {
        if ($item->is_special) {
            $this->addView($item);
        }

        $isOfficial = $item->type_id <= 5;
        $owns = false;
        $favoriteCount = $item->favorites()->active()->count();
        $hasFavorited = false;
        $hasWishlisted = false;
        $hasBuyRequests = -1;
        $serials = "[]";

        if (Auth::check()) {
            $owns = $item->owners()->ownedBy(Auth::id())->exists();
            $hasFavorited = $item->favorites()->active()->userId(Auth::id())->exists();
            $hasWishlisted = $item->isWishlistable() && $item->wishlists()->active()->userId(Auth::id())->exists();
            $hasBuyRequests = Auth::user()->buyRequests()->itemId($item->id)->active()->first()?->bucks ?? -1;
            $serials = Auth::user()->crate()->select('serial', 'id')->itemId($item->id)->whereDoesntHave('specialSeller')->get();
        }

        return view('pages.shop.item')->with([
            'item' => $item,
            'is_official' => $isOfficial,
            'owns' => $owns,
            'favorite_count' => $favoriteCount,
            'has_favorited' => $hasFavorited,
            'has_wishlisted' => $hasWishlisted,
            'has_buy_requests' => $hasBuyRequests,
            'serials' => $serials
        ]);
    }

    /**
     * Add a view count to item cache if the user has not viewed recently
     * 
     * @param \App\Models\Item\Item $item 
     * @return void 
     */
    private function addView(Item $item): void
    {
        if (!Auth::check()) return;

        $collection = collect(Auth::user()->viewed_items);
        $cleanedViewedItems = $collection->filter(fn ($value, $key) => !Carbon::parse($value)->addDay()->isPast());

        if (!$cleanedViewedItems->has($item->id)) {
            $key = "shop" . $item->id . "specialData" . "viewCount";
            // is it worth it to use get/put instead of incr just to add a ttl?
            Cache::put($key, Cache::get($key, 0) + 1, now()->addDays(7));
            $cleanedViewedItems->put($item->id, Carbon::now());
        }

        Cache::put("user" . Auth::id() . "shopViews", $cleanedViewedItems->toJson(), now()->addDays(7));
    }
}
