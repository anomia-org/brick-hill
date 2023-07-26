<?php

namespace App\Http\Controllers\API\User;

use App\Exceptions\Custom\InvalidDataException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User\{
    User,
    Trade
};
use App\Http\Resources\User\Trade\TradeResource;
use App\Http\Resources\User\Trade\MyTradesResource;

class TradeAPIController extends Controller
{
    public function trade(Trade $trade)
    {
        $trade->load(['receiverItems.crateable', 'senderItems.crateable', 'sender.tradeValue', 'receiver.tradeValue']);

        return new TradeResource($trade);
    }

    public function myTrades(Request $request, User $user)
    {
        $query = match ($request->type) {
            'inbound' => $user->trades(),
            'outbound' => $user->outgoingTrades(),
            'history' => $user->tradeHistory()->notPending(),
            'accepted' => $user->tradeHistory()->accepted(),
            'declined' => $user->tradeHistory()->declined(),
            default => throw new InvalidDataException
        };
        $paginator = $query->with('sender', 'receiver')->paginateByCursor(['updated_at' => 'desc', 'id' => 'desc']);
        return MyTradesResource::paginateCollection($paginator);
    }
}
