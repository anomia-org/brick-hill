<?php

namespace App\Http\Controllers\API\Shop;

use App\Extensions\Search\MixedSearch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Item\{
    Item,
    ItemType
};

use App\Http\Resources\Item\ItemResource;
use App\Http\Resources\Item\VersionResource;
use App\Http\Resources\User\CrateResource;

class ItemAPIController extends Controller
{
    /**
     * Return items using more accurate but slower searches using SQL
     * 
     * @param \Illuminate\Http\Request $request 
     * @return \Illuminate\Http\Resources\Json\ResourceCollection<mixed> 
     */
    public function latestItems(Request $request)
    {
        // sort order
        $query = match ($request->sort) {
            'newest' => Item::orderBy('id', 'DESC'),
            'oldest' => Item::orderBy('id', 'ASC'),
                /*'expensive' => Item::whereHas('product', fn ($q) => $q->where('offsale', false))
                ->orderBy(
                    Product::selectRaw('GREATEST(COALESCE(products.bits, 0), COALESCE(products.bucks, 0) * 10)')
                        ->whereColumn('products.productable_id', 'items.id')->orderBy('products.id', 'DESC')->limit(1),
                    'DESC'
                ),
            'inexpensive' => Item::whereHas('product', fn ($q) => $q->where('offsale', false))
                ->orderBy(
                    Product::selectRaw('GREATEST(COALESCE(products.bits, 0), COALESCE(products.bucks, 0) * 10)')
                        ->whereColumn('products.productable_id', 'items.id')->orderBy('products.id', 'DESC')->limit(1),
                    'ASC'
                ),*/
            default => Item::orderBy('updated_at', 'DESC')->orderBy('id', 'DESC')
        };
        // types of items to include
        $types = match (true) {
            $request->type == 'all' => ItemType::officialItemTypes(),
            $request->filled('types') && is_array($request->types) => ItemType::whereIn('name', $request->types),
            $request->filled('type') => ItemType::type($request->type),
            default => ItemType::officialItemTypes()
        };
        $query = $query->whereIn('type_id', $types->get('id'));

        // if a search should be added
        if ($request->filled('search')) {
            $query = $query->where('name', 'like', "%$request->search%");
        }

        // event items
        if ($request->boolean('event_only')) {
            $query = $query->whereNotNull('event_id');
        }

        // special items
        if ($request->boolean('special_only')) {
            $query = $query->where(function ($q) {
                $q->where('special', 1)
                    ->orWhere('special_edition', 1);
            });
        }

        // items that are not for sale
        // must have it filled to ensure compatibility with old api
        if ($request->filled('show_unavailable') && !$request->boolean('show_unavailable')) {
            $query = $query->whereHas('product', fn ($q) => $q->where('offsale', false));
        }

        // only creators that have verified designer
        if ($request->boolean('verified_designers_only')) {
            $query = $query->whereHas('creator', fn ($q) => $q->where('is_verified_designer', true));
        }

        if ($request->filled('tags') && is_array($request->tags)) {
            $query = $query->whereHas('tags', fn ($q) => $q->whereIn('name', $request->tags));
        }

        // TODO: setting this will cause an incompatibility with show_unavailable
        // if its checking where it has a product fitting within a price range it will obviously exclude unavailable items, but thats not exactly made clear
        // if a user sets a max buck price typically you would think NULL would be 'less' than that price and included
        // will need to think of a better way to search this
        // this also creates a second subquery for products since show_unavailable queries the same table, optimize pls :3
        if ($request->filled('min_bucks_price') || $request->filled('max_bucks_price')) {
            $query = $query->whereHas('product', fn ($q) => $q->where([
                ['bucks', '>=', $request->min_bucks_price ?? 0], ['bucks', '<=', $request->max_bucks_price ?? 0]
            ]));
        }

        $query = $query->where([['is_approved', true], ['is_public', true]])->with('creator', 'product', 'itemType', 'event');

        return ItemResource::paginateCollection($query->paginateByCursor());
    }

    /**
     * Return items based on numerous filters retrieved from OpenSearch
     * 
     * @param \Illuminate\Http\Request $request 
     * @return \Illuminate\Http\Resources\Json\ResourceCollection<mixed> 
     */
    public function latestItemsOpensearch(Request $request)
    {
        $query = MixedSearch::search(
            $request->search ?? '',
            function (\OpenSearch\Client $opensearch, string $query, array $options) use ($request) {
                $sort = match ($request->sort) {
                    'newest' => ['id' => 'desc'],
                    'oldest' => ['id' => 'asc'],
                    'expensive' => [
                        'price' => 'desc',
                        'updated_at' => 'desc',
                        'id' => 'desc'
                    ],
                    'inexpensive' => [
                        'price' => 'asc',
                        'updated_at' => 'desc',
                        'id' => 'desc'
                    ],
                    default => [
                        'updated_at' => 'desc',
                        'id' => 'desc'
                    ]
                };
                $options['body']['sort'] = [$sort];

                $opts = $options['body']['query']['bool'] ?? [];
                if ($query) {
                    $opts['must'][0]['simple_query_string']['fields'] = [
                        'name^5', 'description'
                    ];
                }

                // types of items to include
                $types = match (true) {
                    $request->type == 'all' => ItemType::officialItemTypes(),
                    $request->filled('types') && is_array($request->types) => ItemType::whereIn('name', $request->types),
                    $request->filled('type') => ItemType::type($request->type),
                    default => ItemType::officialItemTypes()
                };

                $opts['filter'][]['terms'] = [
                    'type_id' => $types->pluck('id')
                ];

                // event items
                if ($request->boolean('event_only')) {
                    $opts['filter'][]['exists'] = [
                        'field' => 'event_id'
                    ];
                }

                // special items
                if ($request->boolean('special_only')) {
                    $opts['filter'][]['bool']['should'] = [
                        ['term' => ['special_edition' => true]],
                        ['term' => ['special' => true]],
                    ];
                }

                // items that are not for sale
                // must have it filled to ensure compatibility with old api
                if ($request->filled('show_unavailable') && !$request->boolean('show_unavailable')) {
                    $opts['filter'][]['exists'] = [
                        'field' => 'price'
                    ];
                }

                if ($request->filled('min_bucks_price') || $request->filled('max_bucks_price')) {
                    $opts['filter'][]['range'] = [
                        'price' => [
                            'gte' => is_null($request->min_bucks_price) ? NULL : $request->min_bucks_price * 10,
                            'lte' => is_null($request->max_bucks_price) ? NULL : $request->max_bucks_price * 10
                        ]
                    ];
                }

                if (count($opts) > 0) {
                    $options['body']['query']['bool'] = $opts;
                } else {
                    unset($options['body']['query']);
                }

                return $opensearch->search($options);
            }
        )->within(implode(',', [
            (new Item())->searchableAs(),
        ]));

        return ItemResource::paginateCollection($query->cursorPaginate()->load('creator', 'product', 'itemType', 'event', 'cheapestPrivateSeller.user'));
    }

    /**
     * Return items related to an item
     * 
     * @param \App\Models\Item\Item $item 
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection[][] 
     */
    public function relatedItemsv1(Item $item)
    {
        $items = Item::whereIn('id', $item->recommended)->with('creator', 'product', 'itemType', 'event')->get();

        return [
            'data' => [
                'recommended' => ItemResource::collection($items)
            ]
        ];
    }

    public function seriesv1(Item $item)
    {
    }

    public function versionsv1(Item $item)
    {
        return VersionResource::paginateCollection($item->versions()->paginateByCursor(['id' => 'desc']));
    }

    /**
     * Return item data
     * 
     * @param \App\Models\Item\Item $item 
     * @return \App\Http\Resources\Item\ItemResource 
     */
    public function itemv2(Item $item)
    {
        return new ItemResource($item->load('creator', 'product', 'event', 'itemType', 'tags'));
    }

    /**
     * Return cursor paginated owners of an item
     * @param \App\Models\Item\Item $item 
     * @return \Illuminate\Http\Resources\Json\ResourceCollection<mixed> 
     */
    public function ownersv3(Item $item)
    {
        return CrateResource::paginateCollection(
            $item
                ->owners()
                ->with('user:id,username,last_online as include_last_online')
                ->paginateByCursor(['id' => 'asc'])
        );
    }
}
