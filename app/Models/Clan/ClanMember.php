<?php

namespace App\Models\Clan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Clan\ClanMember
 *
 * @property int $id
 * @property int $clan_id
 * @property int $user_id
 * @property int $rank
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clan\Clan $clan
 * @property-read mixed $rank_data
 * @property-read \App\Models\User\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ClanMember accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|ClanMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClanMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClanMember query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClanMember userId($user)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanMember whereClanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanMember whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanMember whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanMember whereUserId($value)
 * @mixin \Eloquent
 */
class ClanMember extends Model
{
    public $fillable = [
        'clan_id', 'user_id', 'rank', 'status'
    ];

    public function clan(): BelongsTo
    {
        return $this->belongsTo('App\Models\Clan\Clan');
    }

    public function getRankDataAttribute()
    {
        $rank = $this->rank;
        return $this->clan->ranks->filter(function ($val, $key) use ($rank) {
            return $val->rank_id == $rank;
        })->first()->only(['name', 'created_at']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User\User')->select(['id', 'username', 'avatar_hash']);
    }

    public function scopeUserId($query, $user)
    {
        return $query->where('user_id', $user);
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }
}
