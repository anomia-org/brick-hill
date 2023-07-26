<?php

namespace App\Models\User\Email;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\User\Email\Email
 *
 * @property int $id
 * @property int $user_id
 * @property string $email
 * @property string|null $verify_code
 * @property bool $verified
 * @property string|null $revert_code
 * @property mixed|null $last_resend
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $send_email
 * @method static Builder|Email newModelQuery()
 * @method static Builder|Email newQuery()
 * @method static Builder|Email query()
 * @method static Builder|Email unverified()
 * @method static Builder|Email userId($id)
 * @method static Builder|Email verified()
 * @method static Builder|Email whereCreatedAt($value)
 * @method static Builder|Email whereEmail($value)
 * @method static Builder|Email whereId($value)
 * @method static Builder|Email whereLastResend($value)
 * @method static Builder|Email whereRevertCode($value)
 * @method static Builder|Email whereUpdatedAt($value)
 * @method static Builder|Email whereUserId($value)
 * @method static Builder|Email whereVerified($value)
 * @method static Builder|Email whereVerifyCode($value)
 * @mixin \Eloquent
 */
class Email extends Model
{
    public $fillable = [
        'user_id', 'email', 'verify_code', 'verified', 'revert_code'
    ];

    protected $hidden = [
        'verify_code'
    ];

    protected $casts = [
        'verified' => 'bool'
    ];

    protected $send_email;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('verified', 'DESC')->orderBy('updated_at', 'DESC');
        });
    }

    public function scopeUserId($query, $id)
    {
        return $query->where('user_id', $id);
    }

    public function scopeVerified($query)
    {
        return $query->where('verified', 1);
    }

    public function scopeUnverified($query)
    {
        return $query->where('verified', 0);
    }

    public function getEmailAttribute($value)
    {
        $this->send_email = $value;
        return preg_replace('/(?<=...).(?=.*@)/u', '*', $value);
    }

    public function getSendEmailAttribute()
    {
        $getEmail = $this->email;
        return $this->send_email;
    }
}
