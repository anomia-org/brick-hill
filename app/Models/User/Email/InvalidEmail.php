<?php

namespace App\Models\User\Email;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User\Email\InvalidEmail
 *
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|InvalidEmail email($e)
 * @method static \Illuminate\Database\Eloquent\Builder|InvalidEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvalidEmail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvalidEmail query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvalidEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvalidEmail whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvalidEmail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InvalidEmail extends Model
{
    protected $primaryKey = 'email';

    public $incrementing = false;

    public $fillable = [
        'email'
    ];

    public function scopeEmail($q, $e)
    {
        return $q->where('email', $e);
    }
}
