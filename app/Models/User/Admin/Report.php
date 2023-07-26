<?php

namespace App\Models\User\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\User\Admin\Report
 *
 * @property \App\Models\Item\Item|\App\Models\Set\Set $reportable
 * @property int $id
 * @property int $user_id
 * @property int $reportable_type
 * @property int $report_reason_type_id
 * @property int $reportable_id
 * @property string|null $report_note
 * @property int $seen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $type
 * @property-read \App\Models\User\Admin\ReportReason $report_reason
 * @property-read \App\Models\User\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder|Report assetId($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report notSeen()
 * @method static \Illuminate\Database\Eloquent\Builder|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder|Report seen()
 * @method static \Illuminate\Database\Eloquent\Builder|Report type($type)
 * @method static \Illuminate\Database\Eloquent\Builder|Report unseen()
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereReportNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereReportReasonTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereReportableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereReportableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUserId($value)
 * @mixin \Eloquent
 */
class Report extends Model
{
	public static $reportable_types = [
		'item' => 1,
		'user' => 2,
		'set' => 3,
		'forumthread' => 4,
		'forumpost' => 5,
		'comment' => 6,
		'message' => 7,
		'clan' => 8
	];

	protected $fillable = [
		'user_id', 'report_reason_type_id', 'reportable_id', 'reportable_type', 'report_note', 'seen'
	];

	public function sender(): BelongsTo
	{
		return $this->belongsTo('App\Models\User\User', 'user_id');
	}

	public function report_reason(): BelongsTo
	{
		return $this->belongsTo('App\Models\User\Admin\ReportReason', 'report_reason_type_id');
	}

	public function reportable(): MorphTo
	{
		return $this->morphTo();
	}

	public function getTypeAttribute()
	{
		return array_search($this->reportable_type, self::$reportable_types);
	}

	public function scopeType($query, $type)
	{
		return $query->where('reportable_type', $type);
	}

	public function scopeAssetId($query, $id)
	{
		return $query->where('reportable_id', $id);
	}

	public function scopeNotSeen($query)
	{
		return $query->where('seen', 0);
	}

	// unseen > notseen
	public function scopeUnseen($query)
	{
		return $query->where('seen', 0);
	}

	public function scopeSeen($query)
	{
		return $query->where('seen', 1);
	}
}
