<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * App\Models\Forum\ForumBookmark
 *
 * @property-read int|null $posts_count
 * @property int $id
 * @property int $user_id
 * @property int $thread_id
 * @property bool $seen
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forum\ForumPost> $posts
 * @property-read \App\Models\Forum\ForumThread $thread
 * @method static Builder|ForumBookmark active()
 * @method static Builder|ForumBookmark newModelQuery()
 * @method static Builder|ForumBookmark newQuery()
 * @method static Builder|ForumBookmark query()
 * @method static Builder|ForumBookmark threadId($threadId)
 * @method static Builder|ForumBookmark unseen()
 * @method static Builder|ForumBookmark userId($userId)
 * @method static Builder|ForumBookmark whereActive($value)
 * @method static Builder|ForumBookmark whereCreatedAt($value)
 * @method static Builder|ForumBookmark whereId($value)
 * @method static Builder|ForumBookmark whereSeen($value)
 * @method static Builder|ForumBookmark whereThreadId($value)
 * @method static Builder|ForumBookmark whereUpdatedAt($value)
 * @method static Builder|ForumBookmark whereUserId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forum\ForumPost> $posts
 * @mixin \Eloquent
 */
class ForumBookmark extends Model
{
    public $fillable = [
        'thread_id', 'user_id', 'seen', 'active'
    ];

    protected $casts = [
        'seen' => 'bool',
        'active' => 'bool'
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function scopeUnseen(Builder $query): Builder
    {
        return $query->where('seen', 0);
    }

    public function scopeUserId(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeThreadId(Builder $query, $threadId): Builder
    {
        return $query->where('thread_id', $threadId);
    }

    /**
     * Return bookmarked thread
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class, 'thread_id');
    }

    /**
     * Return posts from bookmarked thread
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough 
     */
    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(ForumPost::class, ForumThread::class, 'id', 'thread_id', 'thread_id');
    }
}
