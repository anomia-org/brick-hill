<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User\TradeValue
 *
 * @property int $id
 * @property int $user_id
 * @property int $value
 * @property int $direction
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TradeValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TradeValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TradeValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|TradeValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TradeValue whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TradeValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TradeValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TradeValue whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TradeValue whereValue($value)
 * @mixin \Eloquent
 */
class TradeValue extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id', 'value', 'direction'
    ];
}
