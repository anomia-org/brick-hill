<?php

namespace App\Models\User\Email;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User\Email\EmailBlacklist
 *
 * @property string $domain
 * @method static \Illuminate\Database\Eloquent\Builder|EmailBlacklist domain($d)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailBlacklist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailBlacklist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailBlacklist query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailBlacklist whereDomain($value)
 * @mixin \Eloquent
 */
class EmailBlacklist extends Model
{
    protected $table = 'email_blacklist';

    protected $primaryKey = 'domain';

    public $incrementing = false;

    public function scopeDomain($q, $d)
    {
        return $q->where('domain', $d);
    }
}
