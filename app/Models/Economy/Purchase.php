<?php

namespace App\Models\Economy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Models\Item\Item;

/**
 * App\Models\Economy\Purchase
 *
 * @property int $id
 * @property int $user_id
 * @property int $seller_id
 * @property int $product_id
 * @property int|null $crate_id
 * @property int $price
 * @property int|null $pay_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\Crate|null $crate
 * @property-read mixed $pluralized
 * @property-read \App\Models\Economy\Product $product
 * @property-read \App\Models\User\User $purchaser
 * @property-read \App\Models\User\User $seller
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase query()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase soldBy($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase userId($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereCrateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereUserId($value)
 * @mixin \Eloquent
 */
class Purchase extends Model
{
    public $fillable = [
        'user_id', 'seller_id', 'product_id', 'crate_id', 'price', 'pay_id'
    ];

    protected static function booted()
    {
        static::created(function (Purchase $purchase) {
            if (is_a($purchase->product->productable, Item::class))
                $purchase->updateAveragePrice();
            if (is_null($purchase->crate_id))
                Log::error("Purchase created without crate_id");
        });
    }

    private function updateAveragePrice()
    {
        if ($this->pay_id > 2 || !$this->product->productable->is_special)
            return;
        $price = $this->product->productable->average_price;
        $bucks = 0;
        if ($this->pay_id == 0)
            $bucks = $this->price;
        elseif ($this->pay_id == 1)
            $bucks = $this->price / 10;
        if (is_null($price))
            $this->product->productable->average_price = $bucks;
        else
            $this->product->productable->average_price = $this->calculateNextAverage($bucks, $price);
        $this->product->productable->timestamps = false;
        $this->product->productable->save();
    }

    private function calculateNextAverage($bucks, $average)
    {
        $val = ($average * 0.9) + ($bucks * 0.1);
        if ($average > $bucks)
            return floor($val);
        return ceil($val);
    }

    public function scopeUserId($query, $id)
    {
        return $query->where('user_id', $id);
    }

    public function scopeSoldBy($query, $id)
    {
        return $query->where('seller_id', $id);
    }

    // PAY_IDs
    // 0 = BUCKS
    // 1 = BITS
    // 2 = FREE
    // 3 = GRANT
    // 4 = TRANSFER
    // 5 = REDEEMED
    public function getPluralizedAttribute()
    {
        if ($this->pay_id == 5)
            return 'REDEEMED';
        if ($this->pay_id == 4)
            return 'TRANSFERRED';
        if ($this->pay_id == 3)
            return 'GRANTED';
        if ($this->price == 0)
            return 'FREE';
        elseif ($this->pay_id == 0)
            return str_plural('buck', $this->price);
        elseif ($this->pay_id == 1)
            return str_plural('bit', $this->price);
    }

    public function purchaser()
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo('App\Models\User\User', 'seller_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Economy\Product', 'product_id');
    }

    public function crate()
    {
        return $this->belongsTo(\App\Models\User\Crate::class);
    }
}
