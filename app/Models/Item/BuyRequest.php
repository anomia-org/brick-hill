<?php

namespace App\Models\Item;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Item\BuyRequest
 *
 * @property int $id
 * @property int $user_id
 * @property int $item_id
 * @property int $bucks
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Item\Item $item
 * @property-read User $requester
 * @method static \Illuminate\Database\Eloquent\Builder|BuyRequest active()
 * @method static \Database\Factories\Item\BuyRequestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|BuyRequest itemId($item)
 * @method static \Illuminate\Database\Eloquent\Builder|BuyRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuyRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuyRequest order()
 * @method static \Illuminate\Database\Eloquent\Builder|BuyRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuyRequest userId($user)
 * @method static \Illuminate\Database\Eloquent\Builder|BuyRequest whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuyRequest whereBucks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuyRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuyRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuyRequest whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuyRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuyRequest whereUserId($value)
 * @mixin \Eloquent
 */
class BuyRequest extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id', 'item_id', 'bucks', 'active'
    ];

    protected $casts = [
        'active' => 'bool'
    ];

    public function scopeUserId($query, $user)
    {
        return $query->where('user_id', $user);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeItemId($query, $item)
    {
        return $query->where('item_id', $item);
    }

    public function scopeOrder($q)
    {
        return $q->orderBy('bucks', 'DESC')->orderBy('updated_at', 'ASC');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
