<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\{
    Model,
    Builder
};
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use App\Models\User\User;

use App\Traits\Models\User\Admin\Reportable;

/**
 * App\Models\Forum\ForumThread
 *
 * @property int $id
 * @property int $author_id
 * @property int $board_id
 * @property string $title
 * @property string $body
 * @property int $views
 * @property int $locked
 * @property int $pinned
 * @property int $deleted
 * @property int $hidden
 * @property int $scrubbed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $author
 * @property-read \App\Models\Forum\ForumBoard $board
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forum\ForumBookmark> $bookmarks
 * @property-read int|null $bookmarks_count
 * @property-read int $model_author
 * @property-read string $model_url
 * @property-read string $reportable_content
 * @property-read string|null $reportable_image
 * @property bool $viewed
 * @property-read \App\Models\Forum\ForumPost|null $latestPost
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forum\ForumPost> $posts
 * @property int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Admin\Report> $reports
 * @property-read int|null $reports_count
 * @method static Builder|ForumThread authorId($userId)
 * @method static \Database\Factories\Forum\ForumThreadFactory factory($count = null, $state = [])
 * @method static Builder|ForumThread inBoard($boardId)
 * @method static Builder|ForumThread newModelQuery()
 * @method static Builder|ForumThread newQuery()
 * @method static Builder|ForumThread notDeleted($power = 0)
 * @method static Builder|ForumThread popular()
 * @method static Builder|ForumThread query()
 * @method static Builder|ForumThread recent()
 * @method static Builder|ForumThread today()
 * @method static Builder|ForumThread whereAuthorId($value)
 * @method static Builder|ForumThread whereBoardId($value)
 * @method static Builder|ForumThread whereBody($value)
 * @method static Builder|ForumThread whereCreatedAt($value)
 * @method static Builder|ForumThread whereDeleted($value)
 * @method static Builder|ForumThread whereHidden($value)
 * @method static Builder|ForumThread whereId($value)
 * @method static Builder|ForumThread whereLocked($value)
 * @method static Builder|ForumThread wherePinned($value)
 * @method static Builder|ForumThread whereScrubbed($value)
 * @method static Builder|ForumThread whereTitle($value)
 * @method static Builder|ForumThread whereUpdatedAt($value)
 * @method static Builder|ForumThread whereViews($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forum\ForumBookmark> $bookmarks
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Forum\ForumPost> $posts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Admin\Report> $reports
 * @mixin \Eloquent
 */
class ForumThread extends Model
{
    use Reportable, HasFactory;

    protected $fillable = [
        'author_id', 'board_id', 'title', 'body', 'hidden'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('hidden', function (Builder $builder) {
            $builder->where(function (Builder $q) {
                $q->where('hidden', 0)
                    ->orWhere('author_id', Auth::id())
                    ->orWhereRaw('1 = ?', (min(Auth::user()->power ?? 0, 1)));
            });
        });
    }

    public function getModelUrlAttribute(): string
    {
        return url("/forum/thread/{$this->id}");
    }

    public function getModelAuthorAttribute(): int
    {
        return $this->author_id;
    }

    public function getReportableContentAttribute(): string
    {
        return "{$this->title}: {$this->body}";
    }

    public function getReportableImageAttribute(): ?string
    {
        return null;
    }

    public function scopeInBoard(Builder $query, $boardId): Builder
    {
        return $query->where('board_id', $boardId);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->where([['created_at', '>=', Carbon::now()->subDay()], ['deleted', false]]);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('id', 'DESC');
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->withCount('posts')
            ->orderBy('posts_count', 'DESC');
    }

    public function scopeNotDeleted(Builder $query, $power = 0): Builder
    {
        if ($power > 0)
            return $query;
        return $query->where('deleted', 0);
    }

    public function scopeAuthorId(Builder $query, $userId): Builder
    {
        return $query->where('author_id', $userId);
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(ForumBoard::class, 'board_id')->withDefault([
            // if the board doesnt exist (due to a power contraint) return a board model with their user power + 1 so they are basically 'denied'
            // bootleg fix to my stupid decision to use global scopes
            'power' => optional(Auth::user())->power + 1
        ]);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(ForumBookmark::class, 'thread_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(ForumPost::class, 'thread_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function latestPost(): HasOne
    {
        return $this->hasOne(ForumPost::class, 'thread_id')->latestOfMany();
    }

    public function getBodyAttribute(string $value): string
    {
        if ($this->scrubbed)
            return '[ Content Removed ]';
        return $value;
    }

    public function getTitleAttribute(string $value): string
    {
        if ($this->scrubbed)
            return '[ Content Removed ]';
        return $value;
    }

    public function getViewedAttribute(): bool
    {
        return
            $this->attributes['viewed'] ??
            Auth::check() &&
            (array_key_exists($this->id, Auth::user()->viewed_threads) &&
                $this->updated_at <= Carbon::createFromTimestamp(Auth::user()->viewed_threads[$this->id]));
    }

    public function setViewedAttribute(bool $value): void
    {
        $this->attributes['viewed'] = $value;
    }

    public function setPostsCountAttribute(int $value): void
    {
        $this->attributes['posts_count'] = $value;
    }
}
