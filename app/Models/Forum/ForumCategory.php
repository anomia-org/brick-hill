<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Forum\ForumCategory
 *
 * @property int $id
 * @property int $order
 * @property string $title
 * @property string $color
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumCategory whereTitle($value)
 * @mixin \Eloquent
 */
class ForumCategory extends Model
{
	public $timestamps = false;
}
