<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User\DisabledAccount
 *
 * @property int $user_id
 * @property int $type
 * @property-read \App\Models\User\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|DisabledAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DisabledAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DisabledAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|DisabledAccount userId($u)
 * @method static \Illuminate\Database\Eloquent\Builder|DisabledAccount whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisabledAccount whereUserId($value)
 * @mixin \Eloquent
 */
class DisabledAccount extends Model
{
    public function user()
    {
        return $this->belongsTo('App\Models\User\User');
    }

    public function scopeUserId($q, $u)
    {
        return $q->where('user_id', $u);
    }
}
