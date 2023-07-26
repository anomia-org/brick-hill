<?php

namespace App\Models\Polymorphic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Helpers\CursorPaginator;

use App\Traits\Models\User\Admin\Reportable;
use App\Models\Item\Item;
use App\Models\User\User;

/**
 * App\Models\Polymorphic\Comment
 *
 * @property \App\Models\Item\Item|\App\Models\Set\Set $commentable
 * @property int $id
 * @property int $author_id
 * @property int $commentable_id
 * @property int $commentable_type
 * @property string $comment
 * @property bool $scrubbed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $author
 * @property-read int $model_author
 * @property-read string $model_url
 * @property-read string $reportable_content
 * @property-read string|null $reportable_image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Admin\Report> $reports
 * @property-read int|null $reports_count
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereScrubbed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Admin\Report> $reports
 * @mixin \Eloquent
 */
class Comment extends Model
{
    use Reportable;

    protected $casts = [
        'date_formatted' => 'datetime:Y/m/d h:i A',
        'scrubbed' => 'bool'
    ];

    public $fillable = [
        'author_id', 'commentable_id', 'commentable_type', 'comment', 'scrubbed'
    ];

    public function getCommentAttribute($value)
    {
        if ($this->scrubbed)
            return '[ Content Removed ]';
        return $value;
    }

    public function getModelUrlAttribute(): string
    {
        if (!method_exists($this->commentable, 'getModelUrlAttribute'))
            throw new \App\Exceptions\Custom\Internal\InvalidDataException("Comment {$this->id} attempting to generate to commentable with no modelUrl function");
        $cursor = CursorPaginator::encodeCursor([$this->id + 1], 'next')[0];

        return "{$this->commentable->model_url}?commentableCursor={$cursor}";
    }

    public function getModelAuthorAttribute(): int
    {
        return $this->author_id;
    }

    public function getReportableContentAttribute(): string
    {
        return $this->comment;
    }

    public function getReportableImageAttribute(): ?string
    {
        return null;
    }

    /**
     * Returns the Model the Comment belongs to
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, Comment>
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
