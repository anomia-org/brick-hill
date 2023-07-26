<?php

namespace App\Models\User\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User\User;

/**
 * App\Models\User\Admin\AdminLog
 *
 * @property int $id
 * @property int $user_id
 * @property string $action
 * @property mixed $post_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|AdminLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminLog wherePostData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminLog whereUserId($value)
 * @mixin \Eloquent
 */
class AdminLog extends Model
{
	protected $guarded = [];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
