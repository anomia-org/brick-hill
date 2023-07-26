<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\{
    Model,
    Builder
};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Forum\ForumDraft
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $board_id
 * @property string $title
 * @property string $body
 * @property int $deleted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Forum\ForumBoard|null $board
 * @method static Builder|ForumDraft newModelQuery()
 * @method static Builder|ForumDraft newQuery()
 * @method static Builder|ForumDraft notDeleted()
 * @method static Builder|ForumDraft query()
 * @method static Builder|ForumDraft userId($u)
 * @method static Builder|ForumDraft whereBoardId($value)
 * @method static Builder|ForumDraft whereBody($value)
 * @method static Builder|ForumDraft whereCreatedAt($value)
 * @method static Builder|ForumDraft whereDeleted($value)
 * @method static Builder|ForumDraft whereId($value)
 * @method static Builder|ForumDraft whereTitle($value)
 * @method static Builder|ForumDraft whereUpdatedAt($value)
 * @method static Builder|ForumDraft whereUserId($value)
 * @mixin \Eloquent
 */
class ForumDraft extends Model
{
    public $fillable = [
        'user_id', 'board_id', 'title', 'body'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('auth', function (Builder $builder) {
            $builder->where('user_id', (Auth::id() ?? 0));
        });
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo('App\Models\Forum\ForumBoard', 'board_id');
    }

    public function scopeNotDeleted($q)
    {
        return $q->where('deleted', 0);
    }

    public function scopeUserId($q, $u)
    {
        return $q->where('user_id', $u);
    }
}
