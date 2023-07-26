<?php

namespace App\Models\Membership;

use Illuminate\Database\Eloquent\{
    Model,
    Builder
};

/**
 * App\Models\Membership\BillingProduct
 *
 * @property int $id
 * @property string $name
 * @property int $bucks_amount
 * @property int|null $membership
 * @property int|null $membership_length
 * @property string|null $stripe_plan_id
 * @property string|null $paypal_plan_id
 * @property int|null $price_in_cents
 * @property int $active
 * @method static Builder|BillingProduct newModelQuery()
 * @method static Builder|BillingProduct newQuery()
 * @method static Builder|BillingProduct query()
 * @method static Builder|BillingProduct whereActive($value)
 * @method static Builder|BillingProduct whereBucksAmount($value)
 * @method static Builder|BillingProduct whereId($value)
 * @method static Builder|BillingProduct whereMembership($value)
 * @method static Builder|BillingProduct whereMembershipLength($value)
 * @method static Builder|BillingProduct whereName($value)
 * @method static Builder|BillingProduct wherePaypalPlanId($value)
 * @method static Builder|BillingProduct wherePriceInCents($value)
 * @method static Builder|BillingProduct whereStripePlanId($value)
 * @mixin \Eloquent
 */
class BillingProduct extends Model
{
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('power', function (Builder $builder) {
            $builder->where('active', 1);
        });
    }
}
