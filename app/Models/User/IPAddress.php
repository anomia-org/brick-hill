<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\User\IPAddress
 *
 * @property int $id
 * @property int $user_id
 * @property string $ip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \App\Models\User\User $account
 * @method static \Illuminate\Database\Eloquent\Builder|IPAddress excludeUser($id)
 * @method static \Illuminate\Database\Eloquent\Builder|IPAddress ip($i)
 * @method static \Illuminate\Database\Eloquent\Builder|IPAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IPAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IPAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|IPAddress userId($u)
 * @method static \Illuminate\Database\Eloquent\Builder|IPAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IPAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IPAddress whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IPAddress whereUserId($value)
 * @mixin \Eloquent
 */
class IPAddress extends Model
{
    const UPDATED_AT = NULL;
    protected $table = 'ip_addresses';

    public $fillable = [
        'user_id', 'ip'
    ];

    public function scopeUserId($q, $u)
    {
        return $q->where('user_id', $u);
    }

    public function scopeIp($q, $i)
    {
        return $q->where('ip', $i);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo('App\Models\User\User', 'user_id')->select('id', 'username');
    }

    public function scopeExcludeUser($q, $id)
    {
        return $q->where('user_id', '!=', $id);
    }
}
