<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User\BanType
 *
 * @property int $id
 * @property string $name
 * @property string|null $default_note
 * @property int $default_length
 * @method static \Illuminate\Database\Eloquent\Builder|BanType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BanType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BanType query()
 * @method static \Illuminate\Database\Eloquent\Builder|BanType whereDefaultLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BanType whereDefaultNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BanType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BanType whereName($value)
 * @mixin \Eloquent
 */
class BanType extends Model
{
	public $timestamps = false;
}
