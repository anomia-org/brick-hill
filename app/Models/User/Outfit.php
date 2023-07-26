<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

use App\Contracts\Models\IThumbnailable;
use App\Models\Casts\AsCollection;
use App\Models\Item\Item;
use App\Traits\Models\Polymorphic\Thumbnailable;

/**
 * App\Models\User\Outfit
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property \Illuminate\Support\Collection|null $items
 * @property array $variations
 * @property \Illuminate\Support\Collection|null $colors
 * @property int $active
 * @property string $uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $items_list
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Thumbnail> $thumbnails
 * @property-read int|null $thumbnails_count
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit active()
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit query()
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit userId($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit whereColors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit whereItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outfit whereVariations($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Thumbnail> $thumbnails
 * @mixin \Eloquent
 */
class Outfit extends Model implements IThumbnailable
{
    use Thumbnailable;

    public $casts = [
        'items' => AsCollection::class,
        'variations' => 'array',
        'colors' => AsCollection::class
    ];

    protected $fillable = [
        'name',
        'user_id',
        'uuid',
        'active',

        'variations',

        'colors',
        'colors->head', 'colors->torso',
        'colors->left_arm', 'colors->right_arm',
        'colors->right_arm', 'colors->right_leg',

        'items',
        'items->face', 'items->head', 'items->tool', 'items->figure',
        'items->pants', 'items->shirt', 'items->tshirt',

        'items->hats->0', 'items->hats->1', 'items->hats->2', 'items->hats->3', 'items->hats->4',
        'items->clothing->0', 'items->clothing->1', 'items->clothing->2', 'items->clothing->3', 'items->clothing->4'
    ];

    /**
     * Returns if the outfit has a thumbnail
     * 
     * @return bool 
     */
    public function hasThumbnail(): bool
    {
        return true;
    }

    /**
     * Returns outfit color and items for use in renderer
     * 
     * @return array 
     */
    public function getThumbnailData(): array
    {
        if (!$this->items->has('clothing') || count($this->items->get('clothing')) === 0) {
            $this->items->put('clothing', [
                $this->items['pants'],
                $this->items['shirt'],
                $this->items['tshirt'],
            ]);
        }

        $itemIds = $this->items->flatten()->filter()->values();

        $items = Item::whereIn('id', $itemIds)->with('latestAsset')->get();

        $avItems = $this->items->map(function ($item) use ($items) {
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

        return [
            'items' => $avItems,
            'colors' => $this->colors
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeUserId($query, $id)
    {
        return $query->where('user_id', $id);
    }
}
