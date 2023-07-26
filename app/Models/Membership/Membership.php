<?php

namespace App\Models\Membership;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

use App\Models\User\Crate;
use App\Models\User\User;

/**
 * App\Models\Membership\Membership
 *
 * @property int $id
 * @property int $user_id
 * @property int $membership
 * @property string $date
 * @property int $length
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @property-read \App\Models\Membership\MembershipValue $values
 * @method static \Illuminate\Database\Eloquent\Builder|Membership active()
 * @method static \Database\Factories\Membership\MembershipFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Membership newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership query()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership userId($u)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereMembership($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereUserId($value)
 * @mixin \Eloquent
 */
class Membership extends Model
{
    use HasFactory;

    protected $membershipItems = [
        3 => [172591, 172592, 172593, 172594, 172595], // ace items
        4 => [172596, 172597, 172598, 172599, 172600]  // royal items
    ];

    public $fillable = [
        'user_id', 'membership', 'date', 'length', 'active'
    ];

    protected static function booted()
    {
        static::created(function ($membership) {
            $membership->grantMembershipItems();
            $membership->grantOneTimeBonus();
        });
    }

    public function grantOneTimeBonus()
    {
        $hasHadMembership = Membership::where('id', '!=', $this->id)->userId($this->user_id)->exists();
        if (!$hasHadMembership)
            $this->user()->increment('bucks', 100);
    }

    public function grantMembershipItems()
    {
        if (App::environment(['local', 'testing']))
            return;

        $itemsToGrant = $this->membershipItems[$this->membership];
        foreach ($itemsToGrant as $item) {
            $alreadyOwned = Crate::ownedBy($this->user_id)
                ->itemId($item)->exists();
            if (!$alreadyOwned) {
                $this->user->crate()->create([
                    'crateable_id' => $item,
                    'crateable_type' => 1
                ]);
            }
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function values(): BelongsTo
    {
        return $this->belongsTo(MembershipValue::class, 'membership', 'id');
    }

    public function scopeActive($q)
    {
        return $q->where('active', 1);
    }

    public function scopeUserId($q, $u)
    {
        return $q->where('user_id', $u);
    }
}
