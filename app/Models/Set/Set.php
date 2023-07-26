<?php

namespace App\Models\Set;

use App\Models\User\PlayedSet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Facades\Redis;

use App\Traits\Models\{
    User\Admin\Reportable,
    Polymorphic\Assetable,
    Polymorphic\Commentable,
    Polymorphic\Favoriteable,
};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * App\Models\Set\Set
 *
 * @property int $id
 * @property int $creator_id
 * @property int|null $genre_id
 * @property mixed $name
 * @property bool $is_name_scrubbed
 * @property mixed $description
 * @property bool $is_description_scrubbed
 * @property int $playing
 * @property int $visits
 * @property string $address
 * @property string $uid
 * @property int $active
 * @property int|null $max_players
 * @property int|null $friends_only
 * @property bool|null $is_dedicated
 * @property bool|null $is_featured
 * @property string|null $host_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\User\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Favorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Set\GameToken> $gameTokens
 * @property-read int|null $game_tokens_count
 * @property-read string|null $crash_report
 * @property-read int $model_author
 * @property-read string $model_url
 * @property-read string $reportable_content
 * @property-read string|null $reportable_image
 * @property-read mixed $thumbnail
 * @property-read \App\Models\Polymorphic\Asset|null $latestAsset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PlayedSet> $plays
 * @property-read int|null $plays_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Admin\Report> $reports
 * @property-read int|null $reports_count
 * @property-read \App\Models\Set\Server|null $server
 * @property-read \App\Models\Set\SetGenre|null $setGenre
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Set\SetRating> $setRatings
 * @property-read int|null $set_ratings_count
 * @property-read \App\Models\Polymorphic\Asset|null $thumbnailAsset
 * @method static \Illuminate\Database\Eloquent\Builder|Set creatorId($u)
 * @method static \Database\Factories\Set\SetFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Set newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Set newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Set query()
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereFriendsOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereGenreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereHostKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereIsDedicated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereIsDescriptionScrubbed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereIsNameScrubbed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereMaxPlayers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set wherePlaying($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Set whereVisits($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Asset> $assets
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Comment> $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Favorite> $favorites
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Set\GameToken> $gameTokens
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PlayedSet> $plays
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Admin\Report> $reports
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Set\SetRating> $setRatings
 * @mixin \Eloquent
 */
class Set extends Model
{
    use HasFactory, Reportable, Commentable, Favoriteable, Assetable;

    protected $fillable = [
        'creator_id', 'name', 'is_name_scrubbed', 'description', 'is_description_scrubbed', 'playing', 'visits',
        'uid', 'active', 'address', 'max_players', 'friends_only', 'is_dedicated', 'is_featured', 'host_key'
    ];

    protected $hidden = [
        'uid',
        'address',
        'latestAsset',
        'thumbnailAsset',
        'assets',
        'host_key'
    ];

    protected $casts = [
        'is_featured' => 'bool',
        'is_dedicated' => 'bool',
        'is_name_scrubbed' => 'bool',
        'is_description_scrubbed' => 'bool',
    ];

    public function getModelUrlAttribute(): string
    {
        return url("/play/{$this->id}");
    }

    public function getModelAuthorAttribute(): int
    {
        return $this->creator_id;
    }

    public function getReportableContentAttribute(): string
    {
        return "{$this->name}: {$this->description}";
    }

    public function getReportableImageAttribute(): ?string
    {
        return null;
    }

    public function getCrashReportAttribute(): ?string
    {
        return Redis::get("set:{$this->id}:crash");
    }

    /**
     * Overwrite name value if its scrubbed
     * 
     * @param mixed $value 
     * @return mixed 
     */
    public function getNameAttribute($value)
    {
        if ($this->is_name_scrubbed)
            return '[ Content Removed ]';
        return $value;
    }

    /**
     * Overwrite description value if its scrubbed
     * 
     * @param mixed $value 
     * @return mixed 
     */
    public function getDescriptionAttribute($value)
    {
        if ($this->is_description_scrubbed)
            return '[ Content Removed ]';
        return $value;
    }

    public function scopeCreatorId($q, $u)
    {
        return $q->where('creator_id', $u);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo('App\Models\User\User', 'creator_id');
    }

    public function server(): HasOne
    {
        return $this->hasOne('App\Models\Set\Server');
    }

    public function plays(): HasMany
    {
        return $this->hasMany(PlayedSet::class);
    }

    public function setRatings(): HasMany
    {
        return $this->hasMany(SetRating::class);
    }

    public function setGenre(): BelongsTo
    {
        return $this->belongsTo(SetGenre::class, 'genre_id');
    }

    /**
     * Select latestAsset that is an image
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne<\App\Models\Polymorphic\Asset>
     */
    public function thumbnailAsset(): MorphOne
    {
        return $this->latestAsset()->where('asset_type_id', 1);
    }

    public function bundledBrkAssets()
    {
        return $this->assets()->where('asset_type_id', 4);
    }

    public function gameTokens(): HasMany
    {
        return $this->hasMany(GameToken::class);
    }

    public function getThumbnailAttribute()
    {
        $asset = $this->thumbnailAsset;
        if (!$asset) {
            $id = $this->id % 6 + 1;
            return "/images/games/placeholders/{$id}.png";
        }

        if ($asset->is_approved)
            return "/v3/assets/{$asset->uuid}";

        if ($asset->is_pending)
            return "/default/pendingset.png";

        return "/default/declinedset.png";
    }
}
