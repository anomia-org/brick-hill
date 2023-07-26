<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Exceptions\Custom\APIException;
use Illuminate\Support\Facades\{
    Auth,
    Cache,
    DB
};
use App\Http\Controllers\Controller;

use App\Http\Requests\User\Trade\SendTrade;

use App\Models\Item\{
    SpecialSeller
};
use App\Models\User\{
    User,
    Crate,
    Trade
};

class TradeController extends Controller
{
    use \App\Traits\Controllers\PostLimit;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function tradesPage()
    {
        return view('pages.user.trade.trades');
    }

    public function newTrade($uid)
    {
        $user = User::findOrFail($uid);

        if ($uid == Auth::id())
            return redirect()
                ->route('trades');

        return view('pages.user.trade.newtrade')->with([
            'user' => $user
        ]);
    }

    public function newTradeSend(SendTrade $request)
    {
        $user = Auth::user();

        if (!$this->canMakeNewPost($user->allTrades(), 60))
            throw new APIException('You can only send one trade per minute');

        $tradeLimit = $user->membership_limits->items_in_trade;
        if (count($request->giving_items) > $tradeLimit || count($request->asking_items) > $tradeLimit)
            throw new APIException("Trades may not include more than $tradeLimit items on either side");

        $giving_crates = Crate::whereIn('id', $request->giving_items)->with('crateable')->get();
        $asking_crates = Crate::whereIn('id', $request->asking_items)->with('crateable')->get();

        $sender_average_price = $request->giving_bucks;
        $receiver_average_price = $request->asking_bucks;

        // make sure everyone has the items being asked for
        foreach ($giving_crates as $crate) {
            if (!$crate->crateable->isTradeable())
                throw new APIException("Item included in trade is not tradeable");
            if ($crate->user_id != Auth::id() || $crate->own == 0)
                throw new APIException('Sender is missing item in trade');
            $sender_average_price += $crate->crateable->average_price;
        }
        foreach ($asking_crates as $crate) {
            if (!$crate->crateable->isTradeable())
                throw new APIException("Item included in trade is not tradeable");
            if ($crate->user_id != $request->receiver || $crate->own == 0)
                throw new APIException('Receiver is missing item in trade');
            $receiver_average_price += $crate->crateable->average_price;
        }

        \DB::transaction(function () use ($request, $sender_average_price, $receiver_average_price) {
            Trade::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver,
                'sender_item_ids' => $request->giving_items,
                'receiver_item_ids' => $request->asking_items,
                'sender_bucks' => $request->giving_bucks,
                'receiver_bucks' => $request->asking_bucks,
                'sender_avg' => $sender_average_price,
                'receiver_avg' => $receiver_average_price,
            ]);

            if ($request->has('counter')) {
                $trade = Trade::where([['is_pending', true], ['receiver_id', Auth::id()]])->findOrFail($request->counter);
                if ($trade->receiver_id == Auth::id()) {
                    $trade->is_pending = 0;
                    $trade->save();
                }
            }
        });

        return [
            'success' => 'Trade has been sent',
        ];
    }

    public function trade(Request $request)
    {
        $id = $request->trade_id;

        $trade = Trade::with('senderItems', 'receiverItems')->find($id);

        if (!$trade || ($trade->receiver_id != Auth::id() && $trade->sender_id != Auth::id()) || !$trade->is_pending)
            return back();

        switch ($request->type) {
            case 'decline':
                if ($trade->receiver_id == Auth::id()) {
                    $trade->is_pending = 0;
                    $trade->save();
                }
                return error('Trade declined', route('trades'));
            case 'cancel':
                if ($trade->sender_id == Auth::id()) {
                    $trade->is_pending = 0;
                    $trade->is_cancelled = 1;
                    $trade->save();
                }
                return success('Trade cancelled', route('trades'));
            case 'accept':
                if ($trade->receiver_id == Auth::id()) {
                    Cache::lock('trade_' . $trade->id . '_lock', 5)->get(function () use ($trade) {
                        // if they are sending bucks, check if they still have enough bucks
                        $sender = User::find($trade->sender_id);
                        $receiver = User::find($trade->receiver_id);
                        // add taxes
                        if ($sender->bucks < $trade->sender_bucks || $receiver->bucks < $trade->receiver_bucks) {
                            $trade->has_errored = 1;
                            $trade->is_pending = 0;
                            $trade->save();
                            return error('There was an error in the trade.', route('trades'));
                        }

                        // check if everyone still has the items
                        // AND the items are sold out!!
                        foreach ($trade->senderItems as $crate) {
                            if (!$crate->crateable->sold_out || $crate->user_id != $sender->id || $crate->own == 0) {
                                $trade->has_errored = 1;
                                $trade->is_pending = 0;
                                $trade->save();
                                return error('There was an error in the trade.', route('trades'));
                            }
                        }

                        foreach ($trade->receiverItems as $crate) {
                            if (!$crate->crateable->sold_out || $crate->user_id != $receiver->id || $crate->own == 0) {
                                $trade->has_errored = 1;
                                $trade->is_pending = 0;
                                $trade->save();
                                return error('There was an error in the trade.', route('trades'));
                            }
                        }

                        \DB::transaction(function () use ($trade, $sender, $receiver) {
                            // swap the owners of the items

                            // i wonder why i made these foreach instead of an update where query
                            // TODO: might do later
                            foreach ($trade->senderItems as $crate) {
                                // make the item offsale if they are selling it
                                SpecialSeller::crateId($crate->id)->active()->update(['active' => 0]);
                                $crate->user_id = $trade->receiver_id;
                                $crate->save();
                            }

                            foreach ($trade->receiverItems as $crate) {
                                SpecialSeller::crateId($crate->id)->active()->update(['active' => 0]);
                                $crate->user_id = $trade->sender_id;
                                $crate->save();
                            }
                            // give the bucks
                            if ($trade->sender_bucks > 0) {
                                $sender->decrement('bucks', $trade->sender_bucks);
                                $receiver->increment('bucks', round($trade->sender_bucks * $receiver->membership_limits->tax_rate));
                            }
                            if ($trade->receiver_bucks > 0) {
                                $receiver->decrement('bucks', $trade->receiver_bucks);
                                $sender->increment('bucks', round($trade->receiver_bucks * $sender->membership_limits->tax_rate));
                            }
                            $trade->is_pending = 0;
                            $trade->is_accepted = 1;
                            $trade->save();
                        });
                    });
                    return success('Trade accepted', route('trades'));
                }
                break;
            default:
                return back();
        }
    }
}
