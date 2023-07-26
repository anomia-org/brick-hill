<?php

namespace App\Http\Controllers\Events;

use App\Constants\EventNumber;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use App\Models\User\User;
use App\Models\Set\Set;
use App\Models\Item\Item;

use App\Helpers\Event;

use App\Exceptions\Custom\APIException;
use App\Models\Event\EventProgress;

class EventController extends Controller
{
    public function grantItemSession(Request $request, Event $event)
    {
        if (!auth()->check())
            throw new APIException("Not authenticated");

        return [
            'granted' => $event->grantItem(Item::findOrFail($request->item), Auth::user(), $request->key)
        ];
    }

    public function ingameRedeem(Request $request, Event $event)
    {
        $set = Set::where('host_key', $request->host_key)->firstOrFail();

        return [
            'granted' => $event->grantItem(Item::findOrFail($request->item), User::findOrFail($request->user), $set->id, true)
        ];
    }
}
