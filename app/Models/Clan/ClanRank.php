<?php

namespace App\Models\Clan;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Clan\ClanRank
 *
 * @property int $id
 * @property int $clan_id
 * @property int $rank_id
 * @property string $name
 * @property bool $perm_postWall
 * @property bool $perm_modWall
 * @property bool $perm_inviteDecline
 * @property bool $perm_allyEnemy
 * @property bool $perm_changeRank
 * @property bool $perm_addDelRank
 * @property bool $perm_editDesc
 * @property bool $perm_shoutBox
 * @property bool $perm_addFunds
 * @property bool $perm_takeFunds
 * @property bool $perm_editClan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank whereClanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank wherePermAddDelRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank wherePermAddFunds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank wherePermAllyEnemy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank wherePermChangeRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank wherePermEditClan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank wherePermEditDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank wherePermInviteDecline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank wherePermModWall($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank wherePermPostWall($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank wherePermShoutBox($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank wherePermTakeFunds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank whereRankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClanRank whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClanRank extends Model
{
	public $fillable = [
		'clan_id', 'rank_id', 'name',
		'perm_postWall', 'perm_modWall', 'perm_inviteDecline', 'perm_allyEnemy', 'perm_changeRank', 'perm_addDelRank',
		'perm_editDesc', 'perm_shoutBox', 'perm_addFunds', 'perm_takeFunds', 'perm_editClan'
	];

	protected $casts = [
		'perm_postWall' => 'bool',
		'perm_modWall' => 'bool',
		'perm_inviteDecline' => 'bool',
		'perm_allyEnemy' => 'bool',
		'perm_changeRank' => 'bool',
		'perm_addDelRank' => 'bool',
		'perm_editDesc' => 'bool',
		'perm_shoutBox' => 'bool',
		'perm_addFunds' => 'bool',
		'perm_takeFunds' => 'bool',
		'perm_editClan' => 'bool',
	];
}
