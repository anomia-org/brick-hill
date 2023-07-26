<?php

namespace App\Models\Set;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Set\GameToken
 *
 * @property string $token
 * @property int $user_id
 * @property int $set_id
 * @property string $ip
 * @property string|null $validation_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Set\Set $set
 * @property-read User $user
 * @method static \Database\Factories\Set\GameTokenFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|GameToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GameToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GameToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|GameToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameToken whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameToken whereSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameToken whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameToken whereValidationToken($value)
 * @mixin \Eloquent
 */
class GameToken extends Model
{
    use HasFactory;

    public $primaryKey = 'token';
    public $incrementing = false;

    public $fillable = [
        'token', 'user_id', 'set_id', 'ip', 'validation_token'
    ];

    public function set(): BelongsTo
    {
        return $this->belongsTo(Set::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
