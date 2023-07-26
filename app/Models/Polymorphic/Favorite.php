<?php

namespace App\Models\Polymorphic;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Polymorphic\Favorite
 *
 * @property int $id
 * @property int $user_id
 * @property int $favoriteable_id
 * @property int $favoriteable_type
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $favoriteable
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite active()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite userId($u)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereFavoriteableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereFavoriteableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereUserId($value)
 * @mixin \Eloquent
 */
class Favorite extends Model
{
    public $fillable = [
        'user_id', 'favoriteable_id', 'favoriteable_type', 'active'
    ];

    public function scopeActive($q)
    {
        return $q->where('active', true);
    }

    public function scopeUserId($q, $u)
    {
        return $q->where('user_id', $u);
    }

    public function favoriteable()
    {
        return $this->morphTo();
    }
}
