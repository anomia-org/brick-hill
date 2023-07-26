<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\User\Ban
 *
 * @property int $id
 * @property int $user_id
 * @property int $admin_id
 * @property string|null $note
 * @property string|null $content
 * @property int|null $ban_type_id
 * @property int $length
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User\User $admin
 * @property-read \App\Models\User\BanType|null $banType
 * @property-read \App\Models\User\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Ban active()
 * @method static \Database\Factories\User\BanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Ban newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban userId($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereBanTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ban withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Ban withoutTrashed()
 * @mixin \Eloquent
 */
class Ban extends Model
{
    use SoftDeletes, HasFactory;

    public $fillable = [
        'user_id', 'admin_id', 'note', 'content', 'length', 'active', 'ban_type_id'
    ];

    public function getNoteAttribute($value)
    {
        if (is_null($value)) {
            return $this->banType->default_note ?? 'None';
        }
        return $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function banType()
    {
        return $this->hasOne(BanType::class, 'id', 'ban_type_id');
    }

    public function scopeUserId($query, $user)
    {
        return $query->where('user_id', $user);
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
