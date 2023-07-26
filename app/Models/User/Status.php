<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Models\Friend;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\User\Status
 *
 * @property int $id
 * @property int|null $clan_id
 * @property int $owner_id
 * @property string $body
 * @property int $scrubbed
 * @property string $date
 * @property-read \App\Models\Clan\Clan|null $clan
 * @property-read mixed $type
 * @property-read \App\Models\User\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Status clan($clan)
 * @method static \Illuminate\Database\Eloquent\Builder|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status query()
 * @method static \Illuminate\Database\Eloquent\Builder|Status userId($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereClanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereScrubbed($value)
 * @mixin \Eloquent
 */
class Status extends Model
{
    public $timestamps = false;

    public $appends = [
        'type'
    ];

    public $fillable = [
        'type', 'clan_id', 'owner_id', 'body', 'date'
    ];

    public $hidden = [
        'scrubbed'
    ];

    public function getBodyAttribute($value)
    {
        if ($this->scrubbed)
            return '[ Content Removed ]';
        return $value;
    }

    public function scopeUserId($query, $user)
    {
        return $query->where('owner_id', $user)
            ->whereNull('clan_id');
    }

    public function scopeClan($query, $clan)
    {
        return $query->where('clan_id', $clan);
    }

    public function getTypeAttribute()
    {
        if (is_null($this->clan_id))
            return 'user';
        return 'clan';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User\User', 'owner_id');
    }

    public function clan(): BelongsTo
    {
        return $this->belongsTo('App\Models\Clan\Clan', 'clan_id');
    }
}
