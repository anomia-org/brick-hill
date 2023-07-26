<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User\TfaRecoveryCode
 *
 * @property int $user_id
 * @property array $codes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\User $user
 * @method static \Database\Factories\User\TfaRecoveryCodeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|TfaRecoveryCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TfaRecoveryCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TfaRecoveryCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|TfaRecoveryCode whereCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TfaRecoveryCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TfaRecoveryCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TfaRecoveryCode whereUserId($value)
 * @mixin \Eloquent
 */
class TfaRecoveryCode extends Model
{
    use HasFactory;

    public $primaryKey = 'user_id';

    protected $hidden = [
        'codes'
    ];

    public $fillable = [
        'user_id', 'codes'
    ];

    public $casts = [
        'codes' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }
}
