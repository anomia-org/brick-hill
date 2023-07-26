<?php

namespace App\Models\Item;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;
use Laravel\Scout\Searchable;

use Illuminate\Database\Eloquent\Relations\Relation;

use Carbon\Carbon;

use App\Constants\Thumbnails\ThumbnailState;
use App\Contracts\Models\IThumbnailable;
use App\Helpers\Helper;
use App\Models\Economy\Product;
use App\Models\Economy\Purchase;
use App\Models\User\Crate;
use App\Models\User\Avatar;
use App\Models\User\User;
use App\Traits\Models\{
    Economy\Productable,
    User\Admin\Reportable,
    Polymorphic\Taggable,
    Polymorphic\Assetable,
    Polymorphic\Commentable,
    Polymorphic\Favoriteable,
    Polymorphic\Wishlistable,
    Polymorphic\Thumbnailable
};

/**
 * App\Models\Item\Item
 *
 * @property int $id
 * @property int $creator_id
 * @property string|null $name
 * @property string|null $description
 * @property int $type_id
 * @property int|null $series_id
 * @property int|null $event_id
 * @property bool $timer
 * @property \Illuminate\Support\Carbon $timer_date
 * @property bool $special
 * @property bool $special_edition
 * @property int $special_q
 * @property int|null $average_price
 * @property int|null $daily_views
 * @property bool $is_pending
 * @property bool $is_approved
 * @property bool $is_public
 * @property string|null $img_uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item\BuyRequest> $buyRequests
 * @property-read int|null $buy_requests_count
 * @property-read \App\Models\Item\SpecialSeller|null $cheapestPrivateSeller
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Crate> $crates
 * @property-read int|null $crates_count
 * @property-read User $creator
 * @property-read \App\Models\Item\Event|null $event
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Favorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read mixed $approved
 * @property-read mixed $average_price_abbr
 * @property-read mixed $bits
 * @property-read mixed $bucks
 * @property-read mixed $creator_name
 * @property-read mixed $is_special
 * @property-read mixed $items_list
 * @property-read int $model_author
 * @property-read string $model_url
 * @property-read mixed $offsale
 * @property-read mixed $owns
 * @property-read mixed $recommended
 * @property-read string $reportable_content
 * @property-read string|null $reportable_image
 * @property-read mixed $sold
 * @property-read mixed $sold_out
 * @property-read mixed $stock_left
 * @property-read string $thumbnail
 * @property-read mixed $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item\ItemSchedule> $itemSchedule
 * @property-read int|null $item_schedule_count
 * @property-read \App\Models\Item\ItemType $itemType
 * @property-read \App\Models\Polymorphic\Asset|null $latestAsset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Crate> $owners
 * @property-read int|null $owners_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item\SpecialSeller> $privateSellers
 * @property-read int|null $private_sellers_count
 * @property-read Product|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Purchase> $purchases
 * @property-read int|null $purchases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Admin\Report> $reports
 * @property-read int|null $reports_count
 * @property-read \App\Models\Item\Series|null $series
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Thumbnail> $thumbnails
 * @property-read int|null $thumbnails_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item\Version> $versions
 * @property-read int|null $versions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Wishlist> $wishlists
 * @property-read int|null $wishlists_count
 * @method static \Illuminate\Database\Eloquent\Builder|Item approved()
 * @method static \Illuminate\Database\Eloquent\Builder|Item createdBy($u)
 * @method static \Illuminate\Database\Eloquent\Builder|Item declined()
 * @method static \Database\Factories\Item\ItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item pending()
 * @method static \Illuminate\Database\Eloquent\Builder|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder|Item special()
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereAveragePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereDailyViews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereImgUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereIsPending($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereSeriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereSpecial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereSpecialEdition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereSpecialQ($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereTimer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereTimerDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Asset> $assets
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item\BuyRequest> $buyRequests
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Comment> $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Crate> $crates
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Favorite> $favorites
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item\ItemSchedule> $itemSchedule
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Crate> $owners
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item\SpecialSeller> $privateSellers
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Purchase> $purchases
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Admin\Report> $reports
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Tag> $tags
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Thumbnail> $thumbnails
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item\Version> $versions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Wishlist> $wishlists
 * @mixin \Eloquent
 */
class Item extends Model implements IThumbnailable
{
    use Productable,
        Commentable,
        Favoriteable,
        Wishlistable,
        Assetable,
        Reportable,
        Thumbnailable,
        Taggable,
        Searchable,
        HasFactory;

    protected $fillable = [
        'creator_id', 'name', 'description', 'is_approved', 'is_pending', 'is_public',
        'type_id', 'timer', 'timer_date', 'special_edition', 'special_q'
    ];

    protected $hidden = [
        'is_pending', 'is_approved'
    ];

    protected $casts = [
        'timer_date' => 'datetime',
        'timer' => 'bool',
        'is_approved' => 'bool',
        'is_pending' => 'bool',
        'is_public' => 'bool',
        'is_special' => 'bool',
        'special_edition' => 'bool',
        'special' => 'bool'
    ];

    public function scopeApproved($q)
    {
        return $q->where([['is_approved', true], ['is_pending', false]]);
    }

    public function scopePending($q)
    {
        return $q->where('is_pending', true);
    }

    public function scopeDeclined($q)
    {
        return $q->where([['is_approved', false], ['is_pending', false]]);
    }

    public function scopeCreatedBy($q, $u)
    {
        return $q->where('creator_id', $u);
    }

    public function scopeSpecial($q)
    {
        return $q->where('special', true)->orWhere('special_edition', true);
    }

    public function getTypeAttribute()
    {
        return $this->itemType->name;
    }

    public function getApprovedAttribute()
    {
        if ($this->is_pending || !$this->is_approved)
            return false;
        return true;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        if (is_null($this->bucks) && is_null($this->bits)) {
            $price = NULL;
        } else {
            $bucks = !is_null($this->bucks) ? $this->bucks * 10 : PHP_INT_MAX;
            $bits = !is_null($this->bits) ? $this->bits : PHP_INT_MAX;
            $price = min($bucks, $bits);
        }

        if ($this->is_special && $this->sold_out) {
            $price = !is_null($this->cheapestPrivateSeller?->bucks) ? $this->cheapestPrivateSeller->bucks * 10 : NULL;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type_id' => $this->type_id,
            'series_id' => $this->series_id,
            'event_id' => $this->event_id,
            'creator_id' => $this->creator_id,
            'special_q' => $this->special_q,
            'special' => $this->special,
            'special_edition' => $this->special_edition,
            // $this->timer is not an attribute sometimes?
            'timer' => $this->timer ?? false,
            'timer_date' => $this->timer_date,
            'updated_at' => $this->updated_at,
            'price' => $price,
        ];
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        return $this->is_public && $this->is_approved;
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function makeAllSearchableUsing($query)
    {
        return $query->with('product', 'cheapestPrivateSeller');
    }

    /**
     * Returns if the Item is able to be wishlisted
     * 
     * @return bool 
     */
    public function isWishlistable(): bool
    {
        return $this->type_id <= 5;
    }

    /**
     * Returns if the Item is able to be traded
     * 
     * @return bool 
     */
    public function isTradeable(): bool
    {
        return $this->is_special && $this->sold_out;
    }

    /**
     * Returns if the Item has a valid Thumbnail
     * 
     * @return bool 
     */
    public function hasThumbnail(): bool
    {
        return $this->is_approved || $this->is_pending;
    }

    /**
     * Return a pending state if the item isnt approved yet
     * 
     * @return \App\Constants\Thumbnails\ThumbnailState|bool 
     */
    public function overwriteThumbnailState(): ThumbnailState | bool
    {
        if ($this->is_pending || App::environment(['local', 'testing'])) {
            return ThumbnailState::AWAITING_APPROVAL;
        }

        return false;
    }

    /**
     * Return thumbnail data, default colors plus the item
     * 
     * @return array 
     */
    public function getThumbnailData(): array
    {
        $defaultItems = config('site.avatar.default_items');

        if ($this->itemType->name == "hat") {
            $defaultItems['hats'][0] = $this->latestAsset->id;
        } else if (in_array($this->itemType->name, ['pants', 'shirt', 'tshirt'])) {
            $defaultItems['clothing'][0] = $this->latestAsset->id;
        } else {
            $defaultItems[$this->itemType->name] = $this->latestAsset->id;
        }

        return [
            'items' => $defaultItems,
            'colors' => [
                'head' => 'F3B700',
                'torso' => 'B1B1B1',
                'left_arm' => 'F3B700',
                'right_arm' => 'F3B700',
                'left_leg' => 'E9EAEE',
                'right_leg' => 'E9EAEE',
            ]
        ];
    }

    /**
     * Returns a URL to the singleThumbnail endpoint to maintain compatibility on older APIs
     * 
     * @return string 
     */
    public function getThumbnailAttribute(): string
    {
        return config("site.api_url") . "/v1/thumbnails/single?type=2&id=" . $this->id;
    }

    /**
     * Returns if the Item is available to purchase
     * 
     * @return bool 
     */
    public function isPurchasable(): bool
    {
        if (!$this->approved)
            return false;
        if ($this->offsale)
            return false;
        if ($this->special)
            return false;
        if ($this->special_edition && $this->stock_left <= 0)
            return false;
        if ($this->timer && Carbon::parse($this->timer_date)->isPast())
            return false;

        return true;
    }

    public function getModelUrlAttribute(): string
    {
        return url("/shop/{$this->id}");
    }

    public function getModelAuthorAttribute(): int
    {
        return $this->creator_id;
    }

    public function getReportableContentAttribute(): string
    {
        return "{$this->name}: {$this->description}";
    }

    public function getReportableImageAttribute(): ?string
    {
        return $this->thumbnail;
    }

    public function getOffsaleAttribute()
    {
        return $this->product->offsale ?? true;
    }

    public function getBucksAttribute()
    {
        if (!$this->approved || $this->offsale)
            return NULL;

        return $this->product->bucks;
    }

    public function getBitsAttribute()
    {
        if (!$this->approved || $this->offsale)
            return NULL;

        return $this->product->bits;
    }

    public function getTimerAttribute($value)
    {
        if ($value && Carbon::parse($this->timer_date)->isPast()) {
            $this->product->offsale = true;
            $this->product->save();
            $this->timer = false;
            $this->save();
            return false;
        }
        return !!$value;
    }

    public function getSpecialQAttribute($value)
    {
        if ($this->special)
            return (int) $this->sold;
        return $value;
    }

    public function getSoldOutAttribute()
    {
        return $this->is_special && $this->stock_left == 0;
    }

    public function getCreatorNameAttribute()
    {
        return $this->creator->username;
    }

    public function getSoldAttribute()
    {
        $q = Crate::owned()->itemId($this->id);

        if ($this->creator_id != config('site.main_account_id')) {
            $q = $q->where('user_id', '!=', $this->creator_id);
        }

        return Cache::remember('item' . $this->id . 'Sold', 5 * 60, function () use ($q) {
            return $q->count();
        });
    }

    public function getStockLeftAttribute()
    {
        if (!$this->special_edition)
            return 0;

        // Sold is a cached value so if its not equal to the stock we dont want to use that value
        // but there is no point in checking the owner count each time if the cached value is already equal to the stock
        // because its never going to increase past that
        if ($this->sold == $this->special_q)
            return 0;

        return $this->special_q - $this->owners()->count();
    }

    public function getIsSpecialAttribute()
    {
        return ($this->special_edition || $this->special);
    }

    public function getOwnsAttribute()
    {
        if (auth()->check())
            return $this->owners()->ownedBy(auth()->id())->exists();
        else
            return false;
    }

    public function getAveragePriceAbbrAttribute()
    {
        return Helper::numAbbr($this->average_price);
    }

    /**
     * Returns versions related to the item
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany 
     */
    public function versions(): HasMany
    {
        return $this->hasMany(Version::class);
    }

    /**
     * Returns items series
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    /**
     * Returns item event
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Returns item type
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function itemType(): BelongsTo
    {
        return $this->belongsTo(ItemType::class, 'type_id');
    }

    /**
     * Returns associated item schedules
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany 
     */
    public function itemSchedule(): HasMany
    {
        return $this->hasMany(ItemSchedule::class);
    }

    /**
     * Returns the cheapest active private seller
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne 
     */
    public function cheapestPrivateSeller(): HasOne
    {
        return $this->hasOne(SpecialSeller::class)->ofMany(['bucks' => 'min'], function ($q) {
            $q->active();
        });
    }

    /**
     * Returns active associated private sellers
     * @return \Illuminate\Database\Eloquent\Relations\HasMany 
     */
    public function privateSellers(): HasMany
    {
        return $this->hasMany(SpecialSeller::class)->active();
    }

    /**
     * Returns active associated buy requests
     * @return \Illuminate\Database\Eloquent\Relations\HasMany 
     */
    public function buyRequests(): HasMany
    {
        return $this->hasMany(BuyRequest::class)->active();
    }

    /**
     * Returns associated purchases
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough 
     */
    public function purchases(): HasManyThrough
    {
        return $this->hasManyThrough(Purchase::class, Product::class, 'productable_id')
            ->where('products.productable_type', array_search(static::class, Relation::morphMap()));
    }

    /**
     * Returns items creator
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Returns a list of item ids based on how many people are wearing another item with this item
     * 
     * @return mixed 
     */
    public function getRecommendedAttribute()
    {
        return Cache::remember('item' . $this->id . 'Recommended', 60 * 60, function () {
            if ($this->type == 'hat')
                $type = 'hats';
            else
                $type = $this->type;
            $items = Avatar::whereRaw("json_contains(items, '" . $this->id . "', '$." . $type . "')")->get(['items']);

            $results = [];
            $final = [];
            foreach ($items as $render) {
                $render = $render->items;
                foreach ($render['hats'] as $hat) {
                    if ($hat > 0 && $hat != $this->id) {
                        if (!array_key_exists($hat, $results)) {
                            $results[$hat] = 0;
                        }
                        $results[$hat] += 1;
                    }
                }

                foreach ($render as $type => $item) {
                    if ($type != 'hats') {
                        if (!is_array($item) && $item > 0 && $item != $this->id) {
                            if (!array_key_exists($item, $results)) {
                                $results[$item] = 0;
                            }
                            $results[$item] += 1;
                        }
                    }
                }
            }

            $results = array_reverse(array_sort($results), true);
            $results = array_slice($results, 0, 5, true);

            foreach ($results as $key => $value) {
                $item = $this->where('id', $key)->first([
                    'id',
                ])->id;

                $final[] = $item;
            }

            if (count($final) < 5) {
                $required = -count($final) + 5;
                $items = $this
                    ->approved()
                    ->where([['type_id', $this->type_id], ['id', '!=', $this->id]])
                    ->orderByRaw('RAND()')
                    ->limit($required)
                    ->get([
                        'id',
                    ])->keyBy('id')->keys();

                $final = collect($final)->merge($items);
            }

            return $final;
        });
    }
}
