<?php

namespace App\Models\Set;

use Illuminate\Database\Eloquent\Model;

use App\Models\User\User;

/**
 * App\Models\Set\SetRating
 *
 * @property int $id
 * @property int $user_id
 * @property int $set_id
 * @property int $is_liked
 * @property int $is_valid
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Set\Set $set
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating active()
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating query()
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating status(bool $status)
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating userId(int $id)
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating valid()
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating whereIsLiked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating whereIsValid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating whereSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetRating whereUserId($value)
 * @mixin \Eloquent
 */
class SetRating extends Model
{
    public $fillable = [
        'user_id', 'set_id', 'is_liked', 'is_active', 'is_valid'
    ];

    public function scopeStatus($q, bool $status)
    {
        return $q->where('is_liked', $status);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeValid($q)
    {
        return $q->where('is_valid', true);
    }

    public function scopeUserId($q, int $id)
    {
        return $q->where('user_id', $id);
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
