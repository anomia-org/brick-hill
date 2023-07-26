<?php

namespace App\Models\Membership;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Carbon\Carbon;

use App\Models\User\{
    User,
    Award,
    Crate
};

/**
 * App\Models\Membership\Payment
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $gross_in_cents
 * @property string|null $email
 * @property string $receipt
 * @property string|null $product
 * @property int|null $billing_product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Membership\BillingProduct|null $billingProduct
 * @property-read mixed $gross
 * @property-read mixed $real_email
 * @property-read User $user
 * @method static \Database\Factories\Membership\PaymentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment receipt($r)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment userId($u)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereBillingProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereGrossInCents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereReceipt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUserId($value)
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasFactory;

    protected array $donatorItems = [
        2500 => 136998,
        5000 => 338785,
        10000 => 136999
    ];

    public static array $monthlyItems = [
        [
            'startDate' => '2022-07-01 00:00:00',
            'endDate' => '2022-09-30 23:59:59',
            'prizes' => [
                316085 => 2000,
            ],
            'image' => 'taco.png',
            'id' =>  316085
        ],
        [
            'startDate' => '2022-12-25 00:00:00',
            'endDate' => '2022-12-31 23:59:59',
            'prizes' => [
                355068 => 2000,
                355069 => 2000,
                355071 => 2000,
            ],
            'image' => 'bread.png',
            'id' =>  355068
        ],
        [
            'startDate' => '2022-10-01 00:00:00',
            'endDate' => '2022-12-31 23:59:59',
            'prizes' => [
                336552 => 2000,
            ],
            'image' => 'lamp.png',
            'id' =>  336552
        ],
        [
            'startDate' => '2023-01-01 00:00:00',
            'endDate' => '2023-03-31 23:59:59',
            'prizes' => [
                335513 => 2000,
            ],
            'image' => 'magnify.png',
            'id' =>  335513
        ],
        [
            'startDate' => '2023-04-01 00:00:00',
            'endDate' => '2023-06-30 23:59:59',
            'prizes' => [
                361858 => 2000,
            ],
            'image' => 'light.png',
            'id' =>  361858
        ],
    ];

    protected $real_email;

    public $fillable = [
        'user_id', 'gross_in_cents', 'email', 'receipt', 'product', 'billing_product_id'
    ];

    protected static function booted(): void
    {
        static::created(function ($payment) {
            $payment->grantDonatorItems();
            $payment->grantDonatorAward();
            $payment->grantMonthlyItems();
        });
    }

    public function getGrossAttribute()
    {
        return $this->gross_in_cents / 100;
    }

    /**
     * Grant monthly donator items
     * 
     * @return void 
     */
    public function grantMonthlyItems(): void
    {
        $timePeriods = $this->getActiveMonthlyItems();

        if (count($timePeriods) == 0) {
            return;
        }

        foreach ($timePeriods as $time) {
            $total = $this->user->payments()
                // only want to count buck transactions
                ->whereHas('billingProduct', fn ($q) => $q->where('bucks_amount', '>', 0))
                ->where('created_at', '>=', $time['startDate'])
                ->sum('gross_in_cents');
            foreach ($time['prizes'] as $itemId => $amount) {
                if ($total >= $amount) {
                    $this->grantItem($itemId);
                }
            }
        }
    }

    /**
     * Get the current active donator items
     * 
     * @return array 
     */
    public static function getActiveMonthlyItems(): array
    {
        $timePeriods = [];

        foreach (self::$monthlyItems as $time) {
            if (Carbon::parse($time['endDate'])->isPast() || !Carbon::parse($time['startDate'])->isPast()) continue;

            $timePeriods[] = $time;
        }

        return $timePeriods;
    }

    /**
     * Grant the donator award
     * 
     * @return void 
     */
    public function grantDonatorAward(): void
    {
        if (!Award::userId($this->user_id)->type(4)->exists()) {
            Award::create([
                'user_id' => $this->user_id,
                'award_id' => 4
            ]);
        }
    }

    /**
     * Grant permanent donator items
     * 
     * @return void 
     */
    public function grantDonatorItems(): void
    {
        $total = $this->user->payments()->sum('gross_in_cents');

        foreach ($this->donatorItems as $am => $item) {
            if ($total >= $am) {
                $this->grantItem($item);
            }
        }
    }

    /**
     * Grants the Payment user an item. For use in granting donator items.
     * 
     * @param int $itemId 
     * @return void 
     */
    private function grantItem(int $itemId): void
    {
        $alreadyOwned = Crate::ownedBy($this->user_id)
            ->itemId($itemId);
        if ($alreadyOwned->count() == 0) {
            $this->user->crate()->create([
                'crateable_id' => $itemId,
                'crateable_type' => 1
            ]);
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function billingProduct(): BelongsTo
    {
        return $this->belongsTo(BillingProduct::class);
    }

    public function scopeUserId($q, $u)
    {
        return $q->where('user_id', $u);
    }

    public function scopeReceipt($q, $r)
    {
        return $q->where('receipt', $r);
    }

    public function getEmailAttribute($value)
    {
        $this->real_email = $value;
        return preg_replace('/(?<=...).(?=.*@)/u', '*', $value);
    }

    public function getRealEmailAttribute()
    {
        $getEmail = $this->email;
        return $this->real_email;
    }
}
