<?php

namespace App\Models\Event;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Event\PromoCode
 *
 * @property int $id
 * @property string $code
 * @property int $item_id
 * @property int $is_single_use
 * @property int $is_redeemed
 * @property int|null $redeemed_by
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Item\Item $item
 * @property-read \App\Models\User\User|null $redeemedBy
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode code($code)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode notExpired()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereIsRedeemed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereIsSingleUse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereRedeemedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PromoCode extends Model
{
    public $fillable = [
        'code', 'item_id', 'is_single_use', 'is_redeemed', 'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    /**
     * Scope for retrieving PromoCode with code
     * 
     * @param mixed $query 
     * @param mixed $code 
     * @return mixed 
     */
    public function scopeCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Scope for retrieving PromoCodes that arent expired
     * @param mixed $query 
     * @return mixed 
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('expires_at', '>=', Carbon::now())->orWhereNull('expires_at');
        })->where(function ($q) {
            $q->where([
                ['is_single_use', 1],
                ['is_redeemed', 0]
            ])->orWhere('is_single_use', 0);
        });
    }

    /**
     * Returns the User who redeemed the code
     * This only stores data on a single use code
     * 
     * @return BelongsTo 
     */
    public function redeemedBy()
    {
        return $this->belongsTo(\App\Models\User\User::class, 'redeemed_by');
    }

    /**
     * Returns PromoCode item relation
     * 
     * @return BelongsTo 
     */
    public function item()
    {
        return $this->belongsTo(\App\Models\Item\Item::class);
    }
}
