<?php

namespace App\Models\Clan;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Models\User\Admin\Reportable;
use App\Traits\Models\Polymorphic\Assetable;

/**
 * App\Models\Clan\Clan
 *
 * @property int $id
 * @property int $owner_id
 * @property string $tag
 * @property string $title
 * @property string $description
 * @property int $funds
 * @property string $type
 * @property string $ownership
 * @property string $approved
 * @property string|null $clan_hash
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read int $model_author
 * @property-read string $model_url
 * @property-read string $reportable_content
 * @property-read string|null $reportable_image
 * @property-read mixed $thumbnail
 * @property-read \App\Models\Polymorphic\Asset|null $latestAsset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Clan\ClanMember> $members
 * @property-read int|null $members_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Clan\ClanRank> $ranks
 * @property-read int|null $ranks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Admin\Report> $reports
 * @property-read int|null $reports_count
 * @method static \Illuminate\Database\Eloquent\Builder|Clan approved()
 * @method static \Illuminate\Database\Eloquent\Builder|Clan declined()
 * @method static \Illuminate\Database\Eloquent\Builder|Clan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Clan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Clan pending()
 * @method static \Illuminate\Database\Eloquent\Builder|Clan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Clan whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clan whereClanHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clan whereFunds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clan whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clan whereOwnership($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clan whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clan whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clan whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Clan whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Asset> $assets
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Clan\ClanMember> $members
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Clan\ClanRank> $ranks
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Admin\Report> $reports
 * @mixin \Eloquent
 */
class Clan extends Model
{
    use Reportable, Assetable;

    public $fillable = [
        'owner_id', 'tag', 'title', 'description', 'ownership', 'clan_hash', 'funds'
    ];

    public $hidden = [
        'latestAsset',
        'assets'
    ];

    public function getModelUrlAttribute(): string
    {
        return url("/clan/{$this->id}");
    }

    public function getModelAuthorAttribute(): int
    {
        return $this->owner_id;
    }

    public function getReportableContentAttribute(): string
    {
        return "{$this->title} [{$this->tag}]: " . mb_substr($this->description, 0, 100);
    }

    public function getReportableImageAttribute(): ?string
    {
        return $this->thumbnail;
    }

    public function scopeApproved($q)
    {
        return $q->where('approved', 'yes');
    }

    public function scopePending($q)
    {
        return $q->where('approved', 'pending');
    }

    public function scopeDeclined($q)
    {
        return $q->where('approved', 'no');
    }

    public function members()
    {
        return $this->hasMany('App\Models\Clan\ClanMember')->where('status', 'accepted');
    }

    public function ranks()
    {
        return $this->hasMany('App\Models\Clan\ClanRank')->orderBy('rank_id');
    }

    public function getThumbnailAttribute()
    {
        $asset = $this->latestAsset;
        if (!$asset) {
            return config('site.storage.pending.512');
        }

        if ($asset->is_approved)
            return config('site.storage.domain') . "/v3/assets/{$asset->uuid}";

        if ($asset->is_pending)
            return config('site.storage.pending.512');

        return config('site.storage.declined.512');
    }
}
