<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User\AwardType
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AwardType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AwardType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AwardType query()
 * @method static \Illuminate\Database\Eloquent\Builder|AwardType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwardType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AwardType extends Model
{
	public $timestamps = false;
}
