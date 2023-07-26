<?php

namespace App\Models\Item;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Item\ItemType
 *
 * @property int $id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|ItemType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemType officialItemTypes()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemType type($type)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemType whereName($value)
 * @mixin \Eloquent
 */
class ItemType extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    public function scopeType($query, $type)
    {
        return $query->where('name', $type);
    }

    public function scopeOfficialItemTypes($query)
    {
        return $query->where('id', '<=', 5);
    }
}
