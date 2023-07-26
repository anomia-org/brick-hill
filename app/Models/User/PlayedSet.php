<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Set\Set;

/**
 * App\Models\User\PlayedSet
 *
 * @property int $id
 * @property int $user_id
 * @property int $set_id
 * @property mixed|null $left_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Set $set
 * @property-read \App\Models\User\User $user
 * @method static \Database\Factories\User\PlayedSetFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PlayedSet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlayedSet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlayedSet query()
 * @method static \Illuminate\Database\Eloquent\Builder|PlayedSet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlayedSet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlayedSet whereLeftAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlayedSet whereSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlayedSet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlayedSet whereUserId($value)
 * @mixin \Eloquent
 */
class PlayedSet extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id', 'set_id', 'left_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function set(): BelongsTo
    {
        return $this->belongsTo(Set::class);
    }
}
