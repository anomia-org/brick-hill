<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Event\EventProgress
 *
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property int $stage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventProgress atLeastStage($num)
 * @method static \Illuminate\Database\Eloquent\Builder|EventProgress eventId($id)
 * @method static \Illuminate\Database\Eloquent\Builder|EventProgress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventProgress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventProgress query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventProgress userId($id)
 * @method static \Illuminate\Database\Eloquent\Builder|EventProgress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventProgress whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventProgress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventProgress whereStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventProgress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventProgress whereUserId($value)
 * @mixin \Eloquent
 */
class EventProgress extends Model
{
    protected $table = 'event_progresses';

    public $fillable = [
        'user_id', 'event_id', 'stage'
    ];

    public function scopeUserId($query, $id)
    {
        return $query->where('user_id', $id);
    }

    public function scopeEventId($query, $id)
    {
        return $query->where('event_id', $id);
    }

    public function scopeAtLeastStage($query, $num)
    {
        return $query->where('stage', '>=', $num);
    }
}
