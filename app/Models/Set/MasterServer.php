<?php

namespace App\Models\Set;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

/**
 * App\Models\Set\MasterServer
 *
 * @property int $id
 * @property string $ip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MasterServer active()
 * @method static \Illuminate\Database\Eloquent\Builder|MasterServer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MasterServer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MasterServer query()
 * @method static \Illuminate\Database\Eloquent\Builder|MasterServer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MasterServer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MasterServer whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MasterServer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MasterServer extends Model
{
    protected $fillable = [
        'ip'
    ];

    public function scopeActive($q)
    {
        return $q->where('updated_at', '>=', Carbon::now()->subMinutes(2));
    }
}
