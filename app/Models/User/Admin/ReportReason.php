<?php

namespace App\Models\User\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\User\Admin\ReportReason
 *
 * @property int $id
 * @property string $reason
 * @method static Builder|ReportReason newModelQuery()
 * @method static Builder|ReportReason newQuery()
 * @method static Builder|ReportReason query()
 * @method static Builder|ReportReason whereId($value)
 * @method static Builder|ReportReason whereReason($value)
 * @mixin \Eloquent
 */
class ReportReason extends Model
{
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope('order', function (Builder $builder) {
			$builder->orderBy('id', 'ASC');
		});
	}
}
