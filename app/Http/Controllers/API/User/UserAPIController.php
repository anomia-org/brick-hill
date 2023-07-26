<?php

namespace App\Http\Controllers\API\User;

use APIException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\User\{
    User,
    Outfit
};
use App\Models\Item\{
    Item,
    ItemType
};

use App\Http\Resources\User\{
    CrateResource,
    OutfitResource,
    WearingResource
};
use App\Http\Resources\User\Economy\ValueResource;

class UserAPIController extends Controller
{
    public function nameToID(Request $request)
    {
        return User::where('username', $request->username)->firstOrFail(['id', 'username']);
    }

    public function profile(Request $request)
    {
        $user = User::with('status', 'awardsReal.awardType')
            ->select([
                'description',
                'desc_scrubbed',
                'username',
                'id',
                'last_online',
                'created_at',
                'avatar_hash as img',
                'power'
            ])
            ->findOrFail($request->id)
            ->append('awards');
        return $user;
    }

    public function tradeValue(User $user)
    {
        if (!$value = $user->tradeValues()->first())
            throw new ModelNotFoundException("User does not have a value");

        return new ValueResource($value);
    }

    public function userOwnsItem(User $user, Item $item)
    {
        return [
            'owns' => $item->owners()->ownedBy($user->id)->exists()
        ];
    }

    public function crate(Request $request, User $user)
    {
        $typeIds = match (true) {
            $request->type == 'all' || $request->type == 'special' => ItemType::officialItemTypes(),
            $request->filled('types') && is_array($request->types) => ItemType::whereIn('name', $request->types),
            $request->filled('type') => ItemType::type($request->type),
            default => ItemType::officialItemTypes()
        };

        $sort = match ($request->sort) {
            'newest' => ['id' => 'desc'],
            'oldest' => ['id' => 'asc'],
            default => [
                'id' => 'desc'
            ]
        };

        $special_selector = false;
        if (is_array($request->types)) {
            if (in_array('special', $request->types))
                $special_selector = true;
        } else if ($request->type == 'special') {
            $special_selector = true;
        }

        $query = $user->crate()->whereHasMorph('crateable', [Item::class], function ($q) use ($request, $typeIds, $special_selector) {
            $q->where('name', 'like', "%{$request->search}%")
                ->whereIn('type_id', $typeIds->get('id'))
                ->where(function ($q) use ($special_selector) {
                    $q->where('special', '>=', $special_selector ? 1 : 0)
                        ->orWhere('special_edition', '>=', $special_selector ? 1 : 0);
                });

            $priceSort = match ($request->sort) {
                'expensive' => [
                    'average_price' => 'desc',
                    'updated_at' => 'desc',
                    'id' => 'desc'
                ],
                'inexpensive' => [
                    'average_price' => 'asc',
                    'updated_at' => 'desc',
                    'id' => 'desc'
                ],
                default => [
                    'id' => 'desc',
                ]
            };

            foreach ($priceSort as $column => $direction) {
                $q->orderBy($column, $direction);
            }
        });

        return CrateResource::paginateCollection($query->with('crateable')->paginateByCursor($sort));
    }

    /**
     * Formatted wearing data with all item data included
     * 
     * @param \App\Models\User\User $user 
     * @return \APIException|\App\Http\Resources\User\WearingResource 
     */
    public function wearing(User $user)
    {
        $avatar = $user->avatar;

        if (!$avatar)
            throw new APIException("User has no avatar");

        $items = Item::whereIn('id', $avatar->items_list)->get();

        return new WearingResource([
            'colors' => $avatar->colors,
            'items' => $avatar->items,
            'item_models' => $items
        ]);
    }

    /**
     * List outfits by cursor
     * 
     * @return \Illuminate\Http\Resources\Json\ResourceCollection<mixed> 
     */
    public function outfitsv2(Request $request)
    {
        $outfits = Outfit::active()
            ->userId(Auth::id())
            ->where('name', 'like', "%{$request->search}%");

        return OutfitResource::paginateCollection($outfits->paginateByCursor(['id' => 'desc']));
    }

    /**
     * List all outfits
     * DEPRECATED
     * 
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection 
     */
    public function outfits()
    {
        $outfits = Outfit::active()->userId(Auth::id())->get();

        return OutfitResource::collection($outfits);
    }
}
