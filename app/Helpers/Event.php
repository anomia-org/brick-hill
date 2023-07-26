<?php

namespace App\Helpers;

use App\Models\User\User;
use App\Models\Item\Item;
use Illuminate\Support\Facades\Cache;

class Event
{
    public $items = [];

    private $keys = [];

    public function grantItem(Item $item, User $user, $key, $internalRedeem = false)
    {
        if (!$this->isValidKey($item, $key, $internalRedeem))
            return false;

        $lock = Cache::lock('event_' . $user->id . '_lock', 5);
        try {
            $lock->block(10);

            if ($user->crate()->itemId($item->id)->exists())
                return true;

            return $user->crate()->create(['crateable_id' => $item->id, 'crateable_type' => 1])->exists;
        } finally {
            optional($lock)->release();
        }
    }

    private function isValidKey(Item $item, $key, $internalRedeem = false)
    {
        // key must at least contain something
        if (strlen($key) == 0)
            return false;

        // if the key is an int and internalRedeem is false, ignore as its invalid
        if (is_int($key) && !$internalRedeem)
            return false;

        // find values in $this->items that have values equal to the item id
        $grantableKeys = array_keys($this->items, $item->id);

        // no valid keys for that item id
        if (count($grantableKeys) === 0)
            return false;

        foreach ($grantableKeys as $storedKey) {
            $grantableKey = $this->keys[$storedKey];

            // if key is equal to stored key, its valid
            if ($grantableKey === $key) {
                return true;
            }
        }

        return false;
    }
}
