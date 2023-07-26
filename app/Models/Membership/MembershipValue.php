<?php

namespace App\Models\Membership;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Membership\MembershipValue
 *
 * @property int $id
 * @property string $name
 * @property int $daily_bucks
 * @property int $sets
 * @property int $items
 * @property int $create_clans
 * @property int $join_clans
 * @method static \Illuminate\Database\Eloquent\Builder|MembershipValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MembershipValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MembershipValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|MembershipValue whereCreateClans($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MembershipValue whereDailyBucks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MembershipValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MembershipValue whereItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MembershipValue whereJoinClans($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MembershipValue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MembershipValue whereSets($value)
 * @mixin \Eloquent
 */
class MembershipValue extends Model
{
    //
}
