<?php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;
use App\Models\Item\Item;
use App\Models\User\Avatar;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    /**
     * Return an Avatar using Assets instead of Items
     * 
     * @param \App\Models\User\Avatar $avatar 
     * @return \App\Models\User\Avatar 
     */
    public function returnAvatarv2(Avatar $avatar)
    {
        // map items to clothing
        if (!$avatar->items->has('clothing') || count($avatar->items->get('clothing')) === 0) {
            $avatar->items->put('clothing', [
                $avatar->items['pants'],
                $avatar->items['shirt'],
                $avatar->items['tshirt'],
            ]);
        }

        $itemIds = $avatar->items->flatten()->filter()->values();

        $items = Item::whereIn('id', $itemIds)->with('latestAsset')->get();

        // map to latestAsset
        $avItems = $avatar->items->map(function ($item) use ($items) {
            if (is_array($item)) {
                $array = [];

                foreach ($item as $id) {
                    $value = $items->find($id)?->latestAsset?->id;
                    if (is_null($value)) continue;

                    $array[] = $value;
                }

                return $array;
            } else {
                return $items->find($item)?->latestAsset?->id ?? 0;
            }
        });

        $avatar->items = $avItems;

        return $avatar;
    }
}
