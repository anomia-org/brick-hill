<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\Models\User\Admin\Reportable;

/**
 * App\Models\Forum\ForumPost
 *
 * @property int $id
 * @property int $author_id
 * @property int $thread_id
 * @property string $body
 * @property int $scrubbed
 * @property int|null $quote_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\User $author
 * @property-read int $model_author
 * @property-read string $model_url
 * @property-read mixed $page_number
 * @property-read mixed $quotes
 * @property-read string $reportable_content
 * @property-read string|null $reportable_image
 * @property-read ForumPost|null $quote
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Admin\Report> $reports
 * @property-read int|null $reports_count
 * @property-read \App\Models\Forum\ForumThread $thread
 * @method static \Illuminate\Database\Eloquent\Builder|ForumPost authorId($a)
 * @method static \Database\Factories\Forum\ForumPostFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ForumPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumPost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumPost query()
 * @method static \Illuminate\Database\Eloquent\Builder|ForumPost threadId($t)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumPost whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumPost whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumPost whereQuoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumPost whereScrubbed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumPost whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ForumPost whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Admin\Report> $reports
 * @mixin \Eloquent
 */
class ForumPost extends Model
{
    use HasFactory, Reportable;

    protected $fillable = [
        'author_id', 'thread_id', 'body', 'scrubbed', 'quote_id'
    ];

    protected $hidden = [
        'quote'
    ];

    public function getBodyAttribute($value)
    {
        if ($this->scrubbed)
            return '[ Content Removed ]';
        return $value;
    }

    public function getPageNumberAttribute()
    {
        $previous_replies = $this->thread->posts->where('id', '<=', $this->id);
        return ceil($previous_replies->count() / 10);
    }

    public function getModelUrlAttribute(): string
    {
        // could be a performance problem to be potentially running so many counts
        $page = min(ceil(ForumPost::threadId($this->thread_id)->count() / 10), 1);

        return route('thread', ['thread' => $this->thread_id, 'page' => $page, "#post$this->id"]);
    }

    public function getModelAuthorAttribute(): int
    {
        return $this->author_id;
    }

    public function getReportableContentAttribute(): string
    {
        return $this->body;
    }

    public function getReportableImageAttribute(): ?string
    {
        return null;
    }

    public function scopeThreadId($q, $t)
    {
        return $q->where('thread_id', $t);
    }

    public function scopeAuthorId($q, $a)
    {
        return $q->where('author_id', $a);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo('App\Models\Forum\ForumThread', 'thread_id');
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo('App\Models\Forum\ForumPost');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo('App\Models\User\User');
    }

    public function getQuotesAttribute()
    {
        $quote = $this;
        $count = 0;
        $quotes = [];
        do {
            $count++;
            $quote = ($quote->quote_id == $this->quote_id) ? $this->quote : $quote->quote;
            if ($quote) {
                $quotes[] = $quote;
            }
        } while ($quote && $quote->quote_id && $count <= 3);
        return $quotes;
    }
}
