<?php

namespace App\Models\Polymorphic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Polymorphic\Wishlist
 *
 * @property int $id
 * @property int $user_id
 * @property int $wishlistable_id
 * @property int $wishlistable_type
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $wishlistable
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist active()
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist query()
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist userId($u)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereWishlistableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wishlist whereWishlistableType($value)
 * @mixin \Eloquent
 */
class Wishlist extends Model
{
    public $fillable = [
        'user_id', 'wishlistable_id', 'wishlistable_type', 'active'
    ];

    public function scopeActive($q)
    {
        return $q->where('active', true);
    }

    public function scopeUserId($q, $u)
    {
        return $q->where('user_id', $u);
    }

    public function wishlistable(): MorphTo
    {
        return $this->morphTo();
    }
}
