<?php

namespace App\Models\Clan;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Clan\ClanRelation
 *
 * @property int $id
 * @property int $from_clan
 * @property int $to_clan
 * @property string $relation
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $f_clan
 * @property-read mixed $t_clan
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRelation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRelation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRelation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRelation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRelation whereFromClan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRelation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRelation whereRelation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRelation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRelation whereToClan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRelation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClanRelation extends Model
{
    public $fillable = [
        'from_clan', 'to_clan', 'relation', 'status'
    ];

    public $appends = [
        'tclan', 'fclan'
    ];

    public function getTClanAttribute()
    {
        return $this->belongsTo('App\Models\Clan\Clan', 'to_clan', 'id')->first();
    }

    public function getFClanAttribute()
    {
        return $this->belongsTo('App\Models\Clan\Clan', 'from_clan', 'id')->first();
    }
}
