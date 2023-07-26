<?php

namespace App\Http\Controllers\Shop\Transactions;

use App\Exceptions\Custom\APIException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Shop\Transactions\{
    CreateBuyRequest,
    CancelBuyRequest
};

use App\Models\Item\{
    Item,
    BuyRequest
};

use App\Helpers\Event;

class BuyRequestController extends Controller
{
    public function buyRequest(CreateBuyRequest $request, Event $event)
    {
        $item = Item::findOrFail(request('item_id'));

        $total_requests = BuyRequest::userId(Auth::id())->active();
        if ($total_requests->count() >= Auth::user()->membership_limits->buy_requests)
            throw new APIException('You may only have ' . Auth::user()->membership_limits->buy_requests . ' active buy requests');

        // let users place buy orders above the lowest price IF they own the lowest priced item
        $lowest_seller = $item->privateSellers()->orderBy('bucks', 'ASC')->orderBy('updated_at', 'ASC')->orderBy('crate_id', 'ASC')->where('user_id', '!=', Auth::id())->first();
        if ($lowest_seller) {
            if ($lowest_seller->bucks <= request('bucks_amount')) {
                throw new APIException('This item is already on sale at this price or below; purchase it instead');
            }
        }

        $buy_request = BuyRequest::updateOrCreate(
            ['user_id' => Auth::id(), 'item_id' => $item->id],
            ['bucks' => request('bucks_amount'), 'active' => 1]
        );

        if ($buy_request->wasChanged() && !array_key_exists('active', $buy_request->getChanges())) {
            return [
                'success' => 'Your existing buy request was changed to ' . number_format($buy_request->bucks) . ' bucks'
            ];
        } else if (($buy_request->wasChanged() && array_key_exists('active', $buy_request->getChanges())) || $buy_request->wasRecentlyCreated) {
            return [
                'success' => 'A buy request has been successfully listed for ' . number_format($buy_request->bucks) . ' bucks'
            ];
        } else {
            return [
                'success' => 'You already have a buy request for that amount'
            ];
        }
    }

    public function cancelBuyRequest(CancelBuyRequest $request)
    {
        // dont need to even check just run an update query
        BuyRequest::active()
            ->userId(Auth::id())
            ->where('id', $request->id)
            ->update(['active' => 0]);

        return success('The buy request was cancelled');
    }
}
