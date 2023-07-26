<?php

namespace App\Models\Polymorphic;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Polymorphic\AssetType
 *
 * @property int $id
 * @property int $name
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType type($type)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType whereName($value)
 * @mixin \Eloquent
 */
class AssetType extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'name';

    public function scopeType($query, $type)
    {
        return $query->where('name', $type);
    }
}
