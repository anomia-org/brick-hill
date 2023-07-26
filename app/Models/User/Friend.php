<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User\Friend
 *
 * @property int $id
 * @property int $from_id
 * @property int $to_id
 * @property int $is_pending
 * @property int $is_accepted
 * @property-read \App\Models\User\User $fromUser
 * @property-read \App\Models\User\User $toUser
 * @method static \Illuminate\Database\Eloquent\Builder|Friend isAccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Friend isDeclined()
 * @method static \Illuminate\Database\Eloquent\Builder|Friend isPending()
 * @method static \Illuminate\Database\Eloquent\Builder|Friend newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Friend newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Friend query()
 * @method static \Illuminate\Database\Eloquent\Builder|Friend userId($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Friend whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Friend whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Friend whereIsAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Friend whereIsPending($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Friend whereToId($value)
 * @mixin \Eloquent
 */
class Friend extends Model
{
    public $timestamps = false;

    public $fillable = [
        'to_id', 'from_id', 'is_pending', 'is_accepted'
    ];

    public function scopeUserId($q, $user)
    {
        return $q->where(function ($q) use ($user) {
            $q->where('to_id', $user)
                ->orWhere('from_id', $user);
        });
    }

    public function scopeIsAccepted($q)
    {
        return $q->where([['is_pending', false], ['is_accepted', true]]);
    }

    public function scopeIsDeclined($q)
    {
        return $q->where([['is_pending', false], ['is_accepted', false]]);
    }

    public function scopeIsPending($q)
    {
        return $q->where('is_pending', true);
    }

    public function fromUser()
    {
        return $this->belongsTo('App\Models\User\User', 'from_id');
    }

    public function toUser()
    {
        return $this->belongsTo('App\Models\User\User', 'to_id');
    }
}
