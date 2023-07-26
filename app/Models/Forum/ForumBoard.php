<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\{
    Model,
    Builder
};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\{
    Auth,
    Cache
};

/**
 * App\Models\Forum\ForumBoard
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $category_id
 * @property int $power
 * @property-read \App\Models\Forum\ForumCategory|null $category
 * @property-read mixed $post_count_cached
 * @property-read mixed $thread_count_cached
 * @property-read \App\Models\Forum\ForumThread|null $latestThread
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forum\ForumPost> $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forum\ForumThread> $threads
 * @property-read int|null $threads_count
 * @method static Builder|ForumBoard newModelQuery()
 * @method static Builder|ForumBoard newQuery()
 * @method static Builder|ForumBoard query()
 * @method static Builder|ForumBoard whereCategoryId($value)
 * @method static Builder|ForumBoard whereDescription($value)
 * @method static Builder|ForumBoard whereId($value)
 * @method static Builder|ForumBoard whereName($value)
 * @method static Builder|ForumBoard wherePower($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forum\ForumPost> $posts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forum\ForumThread> $threads
 * @mixin \Eloquent
 */
class ForumBoard extends Model
{
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('power', function (Builder $builder) {
            $builder->where('power', '<=', (Auth::user()->power ?? 0));
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'category_id')->withDefault([
            'color' => ''
        ]);
    }

    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(ForumPost::class, ForumThread::class, 'board_id', 'thread_id')->where('forum_threads.deleted', 0);
    }

    public function threads(): HasMany
    {
        return $this->hasMany(ForumThread::class, 'board_id')->where('deleted', 0);
    }

    public function latestThread(): HasOne
    {
        return $this->hasOne(ForumThread::class, 'board_id')->withoutGlobalScope('hidden')->ofMany(['id' => 'max'], function ($q) {
            $q->withoutGlobalScope('hidden');
        });
    }

    public function getPostCountCachedAttribute()
    {
        return Cache::get('forum' . $this->id . 'postCount');
    }

    public function getThreadCountCachedAttribute()
    {
        return Cache::get('forum' . $this->id . 'threadCount');
    }
}
