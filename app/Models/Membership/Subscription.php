<?php

namespace App\Models\Membership;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Membership\Subscription
 *
 * @property int $id
 * @property int $user_id
 * @property string $sub_profile_id
 * @property string $expected_bill
 * @property string|null $product
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription active()
 * @method static \Database\Factories\Membership\SubscriptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription subProfile($s)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription userId($u)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereExpectedBill($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereSubProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUserId($value)
 * @mixin \Eloquent
 */
class Subscription extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id', 'sub_profile_id', 'expected_bill', 'product', 'active'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User\User');
    }

    public function scopeUserId($q, $u)
    {
        return $q->where('user_id', $u);
    }

    public function scopeSubProfile($q, $s)
    {
        return $q->where('sub_profile_id', $s);
    }

    public function scopeActive($q)
    {
        return $q->where('active', 1);
    }
}
