<?php

namespace App\Models\Membership;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Membership\StripeCustomer
 *
 * @property int $user_id
 * @property string|null $stripe_id
 * @property-read \App\Models\User\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|StripeCustomer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeCustomer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeCustomer query()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeCustomer whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeCustomer whereUserId($value)
 * @mixin \Eloquent
 */
class StripeCustomer extends Model
{
    public $primaryKey = 'user_id';
    public $timestamps = false;

    public $fillable = [
        'stripe_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User\User');
    }
}
