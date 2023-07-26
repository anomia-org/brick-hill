<?php

namespace App\Models\Item;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use App\Models\Item\Item;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Item\ItemSchedule
 *
 * @property int $id
 * @property int $item_id
 * @property int $user_id
 * @property int|null $approver_id
 * @property int $is_approved
 * @property bool $hide_update
 * @property int $carried_out
 * @property string|null $name
 * @property string|null $description
 * @property int|null $type_id
 * @property int|null $series_id
 * @property int|null $event_id
 * @property int|null $bits
 * @property int|null $bucks
 * @property int $timer
 * @property \Illuminate\Support\Carbon|null $timer_date
 * @property int $special
 * @property int $special_edition
 * @property int|null $special_q
 * @property \Illuminate\Support\Carbon|null $scheduled_for
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $approver
 * @property-read \App\Models\Item\Event|null $event
 * @property-read Item $item
 * @property-read \App\Models\Item\ItemType|null $itemType
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule notCarriedOut()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule past()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule pendingApproval()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule upcoming()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereApproverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereBits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereBucks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereCarriedOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereHideUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereScheduledFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereSeriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereSpecial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereSpecialEdition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereSpecialQ($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereTimer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereTimerDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemSchedule whereUserId($value)
 * @mixin \Eloquent
 */
class ItemSchedule extends Model
{
    protected $fillable = [
        'item_id', 'user_id', 'approver_id', 'hide_update',
        'name', 'description', 'type_id', 'series_id', 'event_id', 'bits', 'bucks',
        'timer', 'timer_date', 'special', 'special_edition', 'special_q', 'scheduled_for'
    ];

    protected $casts = [
        'hide_update' => 'bool',
        'timer_date' => 'datetime',
        'scheduled_for' => 'datetime'
    ];

    /**
     * Scope for schedules that have not yet been applied
     * 
     * @param mixed $query 
     * @return mixed 
     */
    public function scopeNotCarriedOut($query)
    {
        return $query->where('carried_out', 0);
    }

    /**
     * Scope for schedules that have not yet been approved
     * 
     * @param mixed $query 
     * @return mixed 
     */
    public function scopePendingApproval($query)
    {
        return $query->whereNull('approver_id');
    }

    /**
     * Scope for schedules that have not yet happened
     * 
     * @param mixed $query 
     * @return mixed 
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_for', '>', Carbon::now());
    }

    /**
     * Scope for schedules that have already processed
     * 
     * @param mixed $query 
     * @return mixed 
     */
    public function scopePast($query)
    {
        return $query->where('scheduled_for', '<=', Carbon::now());
    }

    /**
     * Relation for Item scheduled
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relation for User who created the request
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation for User who approved the request
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Type of the item
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function itemType(): BelongsTo
    {
        return $this->belongsTo(ItemType::class, 'type_id');
    }

    /**
     * Event of the item
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
