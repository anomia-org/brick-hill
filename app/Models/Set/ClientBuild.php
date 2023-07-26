<?php

namespace App\Models\Set;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Set\ClientBuild
 *
 * @property int $id
 * @property string $tag
 * @property int $is_release
 * @property int $is_installer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClientBuild newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientBuild newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientBuild query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientBuild whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientBuild whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientBuild whereIsInstaller($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientBuild whereIsRelease($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientBuild whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientBuild whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClientBuild extends Model
{
    public $fillable = [
        'tag', 'is_release', 'is_installer'
    ];
}
