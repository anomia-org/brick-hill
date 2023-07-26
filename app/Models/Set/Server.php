<?php

namespace App\Models\Set;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Set\Server
 *
 * @property int $set_id
 * @property string $ip_address
 * @property int $port
 * @property int $players
 * @property string $last_post
 * @property-read \App\Models\Set\Set $set
 * @method static \Illuminate\Database\Eloquent\Builder|Server newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server query()
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereLastPost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server wherePlayers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereSetId($value)
 * @mixin \Eloquent
 */
class Server extends Model
{
	public $primaryKey = 'set_id';
	public $timestamps = false;

	public $fillable = [
		'set_id', 'ip_address', 'port', 'players', 'last_post'
	];

	public function set()
	{
		return $this->belongsTo(Set::class);
	}
}
