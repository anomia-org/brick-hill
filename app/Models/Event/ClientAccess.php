<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

/**
 * App\Models\Event\ClientAccess
 *
 * @property int $id
 * @property int $user_id
 * @property int $can_debug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccess query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccess whereCanDebug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccess whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccess whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientAccess whereUserId($value)
 * @mixin \Eloquent
 */
class ClientAccess extends Model
{
    public $fillable = [
        'user_id', 'can_debug'
    ];

    protected $itemToGrant = 181877;

    protected static function booted()
    {
        static::created(function ($access) {
            $access->grantItem();
        });
    }

    public function grantItem()
    {
        if (App::environment(['local', 'testing']))
            return;

        $alreadyOwned = $this->user->crate()->itemId($this->itemToGrant)->exists();
        if (!$alreadyOwned) {
            $this->user->crate()->create([
                'crateable_id' => $this->itemToGrant,
                'crateable_type' => 1
            ]);
        }
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User\User::class);
    }
}
