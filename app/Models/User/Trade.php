<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

use App\Models\User\Crate;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Staudenmeir\EloquentJsonRelations\Relations\BelongsToJson;

/**
 * App\Models\User\Trade
 *
 * @property int $id
 * @property int $sender_id
 * @property int $receiver_id
 * @property array|null $sender_item_ids
 * @property array|null $receiver_item_ids
 * @property int $sender_bucks
 * @property int $receiver_bucks
 * @property int $is_accepted
 * @property int $is_pending
 * @property int $is_cancelled
 * @property int $has_errored
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $status
 * @property-read \App\Models\User\User|null $receiver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Crate> $receiverItems
 * @property-read int|null $receiver_items_count
 * @property-read \App\Models\User\User|null $sender
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Crate> $senderItems
 * @property-read int|null $sender_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|Trade accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Trade declined()
 * @method static \Illuminate\Database\Eloquent\Builder|Trade from($u)
 * @method static \Illuminate\Database\Eloquent\Builder|Trade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Trade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Trade notPending()
 * @method static \Illuminate\Database\Eloquent\Builder|Trade pending()
 * @method static \Illuminate\Database\Eloquent\Builder|Trade query()
 * @method static \Illuminate\Database\Eloquent\Builder|Trade to($u)
 * @method static \Illuminate\Database\Eloquent\Builder|Trade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trade whereHasErrored($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trade whereIsAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trade whereIsCancelled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trade whereIsPending($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trade whereReceiverBucks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trade whereReceiverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trade whereReceiverItemIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trade whereSenderBucks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trade whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trade whereSenderItemIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trade whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Crate> $receiverItems
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Crate> $senderItems
 * @mixin \Eloquent
 */
class Trade extends Model
{
	use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

	protected $casts = [
		'sender_item_ids' => 'array',
		'receiver_item_ids' => 'array'
	];

	protected $guarded = [];

	protected $fillable = [];

	public function getStatusAttribute()
	{
		if ($this->is_pending)
			return 'pending';
		if ($this->is_accepted)
			return 'accepted';
		if ($this->is_cancelled)
			return 'cancelled';
		if ($this->has_errored)
			return 'errored';
		return 'declined';
	}

	public function scopeNotPending($q)
	{
		return $q->where('is_pending', false);
	}

	public function scopePending($q)
	{
		return $q->where('is_pending', true);
	}

	public function scopeAccepted($q)
	{
		return $q->where('is_accepted', true);
	}

	public function scopeDeclined($q)
	{
		return $q->where([['is_pending', false], ['is_accepted', false], ['is_cancelled', false], ['has_errored', false]]);
	}

	public function scopeFrom($q, $u)
	{
		return $q->where('sender_id', $u);
	}

	public function scopeTo($q, $u)
	{
		return $q->where('receiver_id', $u);
	}

	public function receiver(): HasOne
	{
		return $this->hasOne('App\Models\User\User', 'id', 'receiver_id')->select(['username', 'avatar_hash', 'id']);
	}

	public function sender(): HasOne
	{
		return $this->hasOne('App\Models\User\User', 'id', 'sender_id')->select(['username', 'avatar_hash', 'id']);
	}

	public function senderItems(): BelongsToJson
	{
		return $this->belongsToJson('App\Models\User\Crate', 'sender_item_ids');
	}

	public function receiverItems(): BelongsToJson
	{
		return $this->belongsToJson('App\Models\User\Crate', 'receiver_item_ids');
	}
}
