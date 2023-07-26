<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

use App\Models\AwardType;

/**
 * App\Models\User\Award
 *
 * @property int $id
 * @property int $user_id
 * @property int $award_id
 * @property int $own
 * @property-read \App\Models\User\AwardType $awardType
 * @property-read mixed $award
 * @method static \Illuminate\Database\Eloquent\Builder|Award newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Award newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Award query()
 * @method static \Illuminate\Database\Eloquent\Builder|Award type($a)
 * @method static \Illuminate\Database\Eloquent\Builder|Award userId($u)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereAwardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereOwn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereUserId($value)
 * @mixin \Eloquent
 */
class Award extends Model
{
    public $timestamps = false;

    protected $appends = [
        'award'
    ];

    protected $hidden = [
        'awardType'
    ];

    protected $fillable = [
        'user_id', 'award_id'
    ];

    public function scopeType($q, $a)
    {
        return $q->where('award_id', $a);
    }

    public function scopeUserId($q, $u)
    {
        return $q->where('user_id', $u);
    }

    public function getAwardAttribute()
    {
        return $this->awardType;
    }

    public function awardType()
    {
        return $this->belongsTo('App\Models\User\AwardType', 'award_id');
    }
}
