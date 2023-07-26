<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User\PastUsername
 *
 * @property int $id
 * @property int $user_id
 * @property string $old_username
 * @property string $new_username
 * @property int $hidden
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername hidden()
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername newName($name)
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername notHidden()
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername oldName($name)
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername query()
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername userId($user)
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername whereNewUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername whereOldUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PastUsername whereUserId($value)
 * @mixin \Eloquent
 */
class PastUsername extends Model
{
    public $fillable = [
        'user_id', 'old_username', 'new_username', 'hidden'
    ];

    public function scopeUserId($query, $user)
    {
        return $query->where('user_id', $user);
    }

    public function scopeHidden($query)
    {
        return $query->where('hidden', true);
    }

    public function scopeNotHidden($query)
    {
        return $query->where('hidden', false);
    }

    public function scopeOldName($query, $name)
    {
        return $query->where('old_username', $name);
    }

    public function scopeNewName($query, $name)
    {
        return $query->where('new_username', $name);
    }
}
