<?php

namespace App\Models\Economy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Economy\Product
 *
 * @property \App\Models\Item\Item|\App\Models\Set\SetPass $productable
 * @property int $id
 * @property int $productable_id
 * @property int $productable_type
 * @property int|null $bits
 * @property int|null $bucks
 * @property bool $offsale
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\Economy\ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBucks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereOffsale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory;

    public $fillable = [
        'productable_id', 'productable_type', 'bits', 'bucks', 'offsale'
    ];

    protected $casts = [
        'offsale' => 'bool'
    ];

    /**
     * Returns the Model the Comment belongs to
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, Product>
     */
    public function productable(): MorphTo
    {
        return $this->morphTo();
    }
}
