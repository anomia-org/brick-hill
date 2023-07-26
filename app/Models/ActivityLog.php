<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ActivityLog
 *
 * @property int $id
 * @property int $users
 * @property int $online
 * @property int $active_subscriptions
 * @property int $active_memberships
 * @property int $posts
 * @property int $bits
 * @property int|null $funds_in_cents
 * @property mixed $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereActiveMemberships($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereActiveSubscriptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereBits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereFundsInCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog wherePosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivityLog whereUsers($value)
 * @mixin \Eloquent
 */
class ActivityLog extends Model
{
	public $timestamps = false;

	protected $fillable = [
		'users', 'online', 'posts', 'bits', 'funds', 'active_subscriptions', 'active_memberships'
	];
}
