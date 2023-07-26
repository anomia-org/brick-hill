<?php

namespace App\Models\Polymorphic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\User\User;

/**
 * App\Models\Polymorphic\Asset
 *
 * @property int $id
 * @property int|null $assetable_id
 * @property int|null $assetable_type
 * @property int $asset_type_id
 * @property int|null $creator_id
 * @property bool $is_approved
 * @property bool $is_pending
 * @property int|null $version
 * @property bool|null $is_selected_version
 * @property bool $is_private
 * @property string $uuid
 * @property string|null $new_format_uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Polymorphic\AssetType|null $assetType
 * @property-read Model|\Eloquent $assetable
 * @property-read User|null $creator
 * @property-read mixed $private_uuid
 * @property-read mixed $url
 * @method static \Database\Factories\Polymorphic\AssetFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Asset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset query()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereAssetTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereAssetableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereAssetableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereIsPending($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereIsSelectedVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereNewFormatUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereVersion($value)
 * @mixin \Eloquent
 */
class Asset extends Model
{
    use HasFactory;

    public $fillable = [
        'assetable_id', 'assetable_type', 'asset_type_id', 'creator_id', 'uuid', 'new_format_uuid', 'is_selected_version', 'is_private'
    ];

    protected $private_uuid;

    protected $casts = [
        'is_approved' => 'bool',
        'is_pending' => 'bool',
        'is_selected_version' => 'bool',
        'is_private' => 'bool'
    ];

    public function assetable()
    {
        return $this->morphTo();
    }

    public function assetType()
    {
        return $this->belongsTo(AssetType::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function getUuidAttribute($value)
    {
        $this->private_uuid = $value;

        if ($this->is_private)
            return NULL;

        return $value;
    }

    public function getPrivateUuidAttribute()
    {
        $v = $this->uuid;
        return $this->private_uuid;
    }

    public function getUrlAttribute()
    {
        return config('site.storage.domain') . "/v3/assets/$this->uuid";
    }
}
