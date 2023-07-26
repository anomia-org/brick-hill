<?php

namespace App\Models\Membership;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Membership\LotteryWinner
 *
 * @property int $id
 * @property int $user_id
 * @property int $amount_won
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|LotteryWinner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LotteryWinner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LotteryWinner query()
 * @method static \Illuminate\Database\Eloquent\Builder|LotteryWinner whereAmountWon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LotteryWinner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LotteryWinner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LotteryWinner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LotteryWinner whereUserId($value)
 * @mixin \Eloquent
 */
class LotteryWinner extends Model
{
    public $fillable = [
        'user_id', 'amount_won'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User\User');
    }
}
