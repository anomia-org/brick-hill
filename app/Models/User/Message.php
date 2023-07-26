<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Models\User\Admin\Reportable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\User\Message
 *
 * @property int $id
 * @property int $author_id
 * @property int $recipient_id
 * @property string $title
 * @property string $message
 * @property int $read
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $model_author
 * @property-read string $model_url
 * @property-read string $reportable_content
 * @property-read string|null $reportable_image
 * @property-read \App\Models\User\User $receiver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Admin\Report> $reports
 * @property-read int|null $reports_count
 * @property-read \App\Models\User\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Message unread()
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereRecipientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Admin\Report> $reports
 * @mixin \Eloquent
 */
class Message extends Model
{
    use Reportable;

    public $fillable = [
        'author_id', 'recipient_id', 'title', 'message', 'read'
    ];

    public function getModelUrlAttribute(): string
    {
        return url("/message/{$this->id}");
    }

    public function getModelAuthorAttribute(): int
    {
        return $this->author_id;
    }

    public function getReportableContentAttribute(): string
    {
        return "{$this->title}: {$this->message}";
    }

    public function getReportableImageAttribute(): ?string
    {
        return null;
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo('App\Models\User\User', 'author_id', 'id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo('App\Models\User\User', 'recipient_id', 'id');
    }

    public function scopeUnread($q)
    {
        return $q->where('read', 0);
    }
}
