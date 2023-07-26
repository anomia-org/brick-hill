<?php

namespace App\Models\User;

use App\Models\Clan\Clan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;

use Carbon\Carbon;

use Illuminate\Support\Facades\{
    Cache,
    Redis
};

use App\Models\Item\Item;
use App\Models\Polymorphic\Comment;
use App\Models\Clan\ClanMember;
use App\Models\Economy\Purchase;
use App\Models\Event\ClientAccess;
use App\Models\Forum\ForumPost;
use App\Models\Forum\ForumThread;
use App\Models\Item\BuyRequest;
use App\Models\Membership\Membership;
use App\Models\Membership\Payment;
use App\Models\Membership\StripeCustomer;
use App\Models\Membership\Subscription;
use App\Models\Set\Set;
use App\Models\User\Admin\Report;
use App\Models\User\Email\Email;
use App\Traits\Models\Polymorphic\Thumbnailable;
use App\Traits\Models\User\Admin\Reportable;

use App\Contracts\Models\IThumbnailable;

/**
 * App\Models\User\User
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string|null $secret_2fa
 * @property string|null $description
 * @property bool $desc_scrubbed
 * @property string $created_at
 * @property \Illuminate\Support\Carbon $last_online
 * @property string $daily_bits
 * @property int $bucks
 * @property int $bits
 * @property int|null $primary_clan_id
 * @property int $playing
 * @property string $last_played
 * @property string $avatar_hash
 * @property int $theme
 * @property string|null $birth
 * @property int|null $gender
 * @property int $power
 * @property bool|null $is_verified_designer
 * @property int $admin_points
 * @property string|null $remember_token
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Trade> $allTrades
 * @property-read int|null $all_trades_count
 * @property-read \App\Models\User\Avatar|null $avatar
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Award> $awardsReal
 * @property-read int|null $awards_real_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Ban> $bans
 * @property-read int|null $bans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, BuyRequest> $buyRequests
 * @property-read int|null $buy_requests_count
 * @property-read ClientAccess|null $clientAccess
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Passport\Client> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Crate> $crate
 * @property-read int|null $crate_count
 * @property-read \App\Models\User\DisabledAccount|null $disabledAccount
 * @property-read Email|null $email
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Email> $emails
 * @property-read int|null $emails_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Friend> $friendRequests
 * @property-read int|null $friend_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Friend> $friends
 * @property-read int|null $friends_count
 * @property-read mixed $avatar_thumbnail
 * @property-read mixed $awards
 * @property-read mixed $disabled
 * @property-read mixed $feed
 * @property-read mixed $is_admin
 * @property-read bool $is_beta_tester
 * @property-read mixed $is_online
 * @property-read mixed $items_list
 * @property-read mixed $linked_accounts
 * @property-read mixed $membership_limits
 * @property-read int $model_author
 * @property-read string $model_url
 * @property-read mixed $post_count
 * @property-read string $reportable_content
 * @property-read string|null $reportable_image
 * @property-read mixed $streak
 * @property-read mixed $tfa_active
 * @property-read mixed $viewed_items
 * @property-read mixed $viewed_threads
 * @property-read mixed $visits
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\IPAddress> $ips
 * @property-read int|null $ips_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read Membership|null $membership
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Outfit> $outfits
 * @property-read int|null $outfits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Trade> $outgoingTrades
 * @property-read int|null $outgoing_trades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\PastUsername> $pastUsernames
 * @property-read int|null $past_usernames_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\PlayedSet> $playedSets
 * @property-read int|null $played_sets_count
 * @property-read Clan|null $primaryClan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Purchase> $purchases
 * @property-read int|null $purchases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Message> $receivedMessages
 * @property-read int|null $received_messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Report> $reports
 * @property-read int|null $reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Message> $sentMessages
 * @property-read int|null $sent_messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Report> $sent_reports
 * @property-read int|null $sent_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Set> $sets
 * @property-read int|null $sets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Status> $status
 * @property-read int|null $status_count
 * @property-read StripeCustomer|null $stripeCustomer
 * @property-read Subscription|null $subscription
 * @property-read \App\Models\User\TfaRecoveryCode|null $tfaRecoveryCodes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Thumbnail> $thumbnails
 * @property-read int|null $thumbnails_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Token> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Trade> $tradeHistory
 * @property-read int|null $trade_history_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\TradeValue> $tradeValues
 * @property-read int|null $trade_values_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Trade> $trades
 * @property-read int|null $trades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ForumPost> $userPosts
 * @property-read int|null $user_posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ForumThread> $userThreads
 * @property-read int|null $user_threads_count
 * @method static \Database\Factories\User\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAdminPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatarHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBucks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDailyBits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDescScrubbed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsVerifiedDesigner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastPlayed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePlaying($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePower($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePrimaryClanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSecret2fa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Trade> $allTrades
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Award> $awardsReal
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Ban> $bans
 * @property-read \Illuminate\Database\Eloquent\Collection<int, BuyRequest> $buyRequests
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Passport\Client> $clients
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Crate> $crate
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Email> $emails
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Friend> $friendRequests
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Friend> $friends
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\IPAddress> $ips
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Outfit> $outfits
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Trade> $outgoingTrades
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\PastUsername> $pastUsernames
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Payment> $payments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission\Permission> $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\PlayedSet> $playedSets
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Purchase> $purchases
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Message> $receivedMessages
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Report> $reports
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission\Role> $roles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Message> $sentMessages
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Report> $sent_reports
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Set> $sets
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Status> $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Thumbnail> $thumbnails
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Token> $tokens
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Trade> $tradeHistory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\TradeValue> $tradeValues
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Trade> $trades
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ForumPost> $userPosts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ForumThread> $userThreads
 * @mixin \Eloquent
 */
class User extends Authenticatable implements IThumbnailable
{
    use HasRoles,
        Reportable,
        Thumbnailable,
        Searchable,
        HasApiTokens,
        HasFactory,
        Notifiable;

    public $timestamps = false;

    protected $hidden = [
        'password', 'remember_token', 'awardsReal', 'power', 'bucks', 'bits', 'secret_2fa', 'tfa_active', 'desc_scrubbed'
    ];

    protected $guarded = [
        'remember_token', 'power'
    ];

    protected $casts = [
        'include_last_online' => 'datetime',
        'last_online' => 'datetime',
        'desc_scrubbed' => 'bool',
        'tfa_active' => 'bool',
        'is_verified_designer' => 'bool'
    ];

    // tard brain webhook spot
    public function routeNotificationForDiscord($notification)
    {
        return 'https://discordapp.com/api/webhooks/703357362836209704/hI88Vo-XeAS2QlDGxzBTdQ61UMzOQvC32HmR9oQ-zgCq4rrbtcPwLbjlB-aeU-8kDY9p';
    }

    public function getModelUrlAttribute(): string
    {
        return url("/user/{$this->id}");
    }

    public function getModelAuthorAttribute(): int
    {
        return $this->id;
    }

    public function getReportableContentAttribute(): string
    {
        return "{$this->username}: {$this->description}";
    }

    public function getReportableImageAttribute(): ?string
    {
        return $this->avatar_thumbnail;
    }

    public function getThemeAttribute(int $value): int
    {
        $maps = [
            1 => 1,
            2 => 2,
            3 => 1,
            4 => 2,
        ];
        return $maps[min($value, 4)];
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->username,
            'past_usernames' => $this->pastUsernames->pluck('new_username'),
            'last_online' => $this->last_online
        ];
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function makeAllSearchableUsing($query)
    {
        return $query->with('pastUsernames');
    }

    /**
     * Returns if the User has a valid Thumbnail
     * 
     * @return bool 
     */
    public function hasThumbnail(): bool
    {
        return true;
    }

    /**
     * Returns avatar colors and items for use in renderer
     * 
     * @return array 
     */
    public function getThumbnailData(): array
    {
        if (is_null($this->avatar)) {
            return [
                'items' => config('site.avatar.default_items'),
                'colors' => config('site.avatar.default_colors')
            ];
        }

        if (!$this->avatar->items->has('clothing') || count($this->avatar->items->get('clothing')) === 0) {
            $this->avatar->items->put('clothing', [
                $this->avatar->items['pants'],
                $this->avatar->items['shirt'],
                $this->avatar->items['tshirt'],
            ]);
        }

        $itemIds = $this->avatar->items->flatten()->filter()->values();

        $items = Item::whereIn('id', $itemIds)->whereHas('latestAsset', function ($q) {
            $q->where('is_approved', 1);
        })->with('latestAsset')->get();

        $avItems = $this->avatar->items->map(function ($item) use ($items) {
            if (is_array($item)) {
                $array = [];

                foreach ($item as $id) {
                    $value = $items->find($id)?->latestAsset?->id;
                    if (is_null($value)) continue;

                    $array[] = $value;
                }

                return $array;
            } else {
                return $items->find($item)?->latestAsset?->id ?? 0;
            }
        });

        return [
            'items' => $avItems,
            'colors' => $this->avatar->colors
        ];
    }

    /**
     * Determines if a user is a beta tester
     * 
     * @return bool 
     */
    public function getIsBetaTesterAttribute()
    {
        $testers = [
            21, 37, 46, 58, 466, 484, 772, 782, 799, 833, 925, 1156, 1691, 2922, 5715, 5279, 6275, 6765,
            9755, 41209, 49974, 82493, 83101, 86632, 96775, 118794, 127702, 182115, 200280, 251304, 253636,
            292948, 309776, 541054
        ];

        return in_array($this->id, $testers) || $this->is_admin || $this->clientAccess()->where('can_debug', true)->exists();
    }

    public function getAvatarThumbnailAttribute()
    {
        return config("site.api_url") . "/v1/thumbnails/single?type=1&id=" . $this->id;
    }

    public function getDescriptionAttribute($value)
    {
        if ($this->desc_scrubbed)
            return '[ Content Removed ]';
        return $value;
    }

    public function getTfaActiveAttribute()
    {
        return !is_null($this->secret_2fa) && strlen($this->secret_2fa) > 0;
    }

    public function getIsAdminAttribute()
    {
        return $this->power > 0;
    }

    public function getGenderAttribute($value)
    {
        switch ($value) {
            case 0:
                return 'male';
            case 1:
                return 'female';
            case 2:
                return 'hidden';
            default:
                return 'unknown';
        }
    }

    public function getCount($type)
    {
        switch ($type) {
            case 'messages':
                return Message::where([['read', 0], ['recipient_id', $this->id]])->count();
            case 'friends':
                return $this->friendRequests->count();
            case 'trades':
                return Trade::pending()->to($this->id)->count();
            case 'admin':
                if ($this->power > 0) {
                    $itemCount = Item::where('is_pending', true)->count();
                    $reportCount = Report::unseen()->count();
                    return $itemCount + $reportCount;
                }
                return 0;
            default:
                return 0;
        }
    }

    public function getStreakAttribute()
    {
        $streak = 0;
        $subscription = $this->subscription;
        if (!is_null($subscription)) {
            if ($subscription->active) {
                $streak = Carbon::parse($subscription->created_at)->diffInDays(Carbon::now());
            }
        }
        return $streak;
    }

    public function getDisabledAttribute()
    {
        return $this->disabledAccount?->exists();
    }

    public function getIsOnlineAttribute()
    {
        return Carbon::now()->diffInMinutes($this->last_online) < 3;
    }

    public function getLinkedAccountsAttribute()
    {
        $ips = $this->ips->pluck('ip')->unique();
        return IPAddress::whereIn('ip', $ips)
            ->selectRaw('user_id, count("user_id") as matched')
            ->groupBy('user_id')
            ->orderBy('matched', 'DESC')
            ->excludeUser($this->id)
            ->with('account')
            ->get();
    }

    public function sent_reports(): HasMany
    {
        return $this->hasMany(Report::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'author_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'creator_id');
    }

    public function playedSets(): HasMany
    {
        return $this->hasMany(PlayedSet::class);
    }

    public function tfaRecoveryCodes(): HasOne
    {
        return $this->hasOne(TfaRecoveryCode::class);
    }

    public function disabledAccount(): HasOne
    {
        return $this->hasOne(DisabledAccount::class);
    }

    public function email(): HasOne
    {
        return $this->hasOne(Email::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    public function sets(): HasMany
    {
        return $this->hasMany(Set::class, 'creator_id');
    }

    public function status(): HasMany
    {
        return $this->hasMany(Status::class, 'owner_id')->whereNull('clan_id')->orderBy('date', 'DESC')->limit(1);
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id', 'id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'author_id', 'id');
    }

    public function avatar(): HasOne
    {
        return $this->hasOne(Avatar::class);
    }

    public function outfits(): HasMany
    {
        return $this->hasMany(Outfit::class);
    }

    public function ips(): HasMany
    {
        return $this->hasMany(IPAddress::class);
    }

    public function bans(): HasMany
    {
        return $this->hasMany(Ban::class);
    }

    public function friends(): HasMany
    {
        return $this->hasMany(Friend::class, 'to_id', 'id')->orWhere('from_id', $this->id);
    }

    public function friendRequests(): HasMany
    {
        return $this->hasMany(Friend::class, 'to_id', 'id')->where('is_pending', true);
    }

    public function pastUsernames(): HasMany
    {
        return $this->hasMany(PastUsername::class, 'user_id', 'id')->notHidden();
    }

    public function crate(): HasMany
    {
        return $this->hasMany(Crate::class)->owned();
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function buyRequests(): HasMany
    {
        return $this->hasMany(BuyRequest::class)->where('active', 1);
    }

    public function membership(): HasOne
    {
        return $this->hasOne(Membership::class)->where('active', 1);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->orderBy('id', 'desc');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function stripeCustomer(): HasOne
    {
        return $this->hasOne(StripeCustomer::class);
    }

    public function awardsReal(): HasMany
    {
        return $this->hasMany(Award::class, 'user_id', 'id')->where('award_id', '>', 1);
    }

    // TODO: this should be called trades and the rest should probably be removed or renamed
    public function allTrades(): HasMany
    {
        return $this->hasMany(Trade::class, 'sender_id', 'id');
    }

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class, 'receiver_id', 'id')
            ->pending();
    }

    public function tradeValue(): HasOne
    {
        return $this->hasOne(TradeValue::class)->orderByDesc('id');
    }

    public function tradeValues(): HasMany
    {
        return $this->hasMany(TradeValue::class)->orderByDesc('id');
    }

    public function outgoingTrades(): HasMany
    {
        return $this->hasMany(Trade::class, 'sender_id', 'id')
            ->pending();
    }

    public function tradeHistory(): HasMany
    {
        return $this->hasMany(Trade::class, 'receiver_id', 'id')
            ->orWhere('sender_id', $this->id);
    }

    public function clientAccess(): HasOne
    {
        return $this->hasOne(ClientAccess::class);
    }

    public function userThreads(): HasMany
    {
        return $this->hasMany(ForumThread::class, 'author_id', 'id');
    }

    public function userPosts(): HasMany
    {
        return $this->hasMany(ForumPost::class, 'author_id', 'id');
    }

    public function primaryClan(): HasOne
    {
        return $this->hasOne(Clan::class, 'id', 'primary_clan_id');
    }

    public function getMembershipLimitsAttribute()
    {
        $mem = $this->membership;
        if (!$mem) {
            return (object) [
                'tax_rate' => 0.8,
                'buy_requests' => 5,
                'specials_onsale' => 5,
                'create_clans' => 1,
                'join_clans' => 5,
                'sets' => 1,
                'items_in_trade' => 36
            ];
        }

        switch ($mem->membership) {
            case 2:
                return (object) [
                    'tax_rate' => 0.8,
                    'buy_requests' => 10,
                    'specials_onsale' => 0,
                    'create_clans' => 5,
                    'join_clans' => 10,
                    'sets' => 10,
                    'items_in_trade' => 100
                ];
            case 3:
                return (object) [
                    'tax_rate' => 0.85,
                    'buy_requests' => 20,
                    'specials_onsale' => 0,
                    'create_clans' => 10,
                    'join_clans' => 20,
                    'sets' => 10,
                    'items_in_trade' => 100
                ];
            case 4:
                return (object) [
                    'tax_rate' => 0.9,
                    'buy_requests' => 50,
                    'specials_onsale' => 0,
                    'create_clans' => 20,
                    'join_clans' => 50,
                    'sets' => 20,
                    'items_in_trade' => 100
                ];
        }
    }

    public function getAwardsAttribute()
    {
        $relationship = $this->awardsReal;
        if ($this->power > \App\Constants\AdminPower::VISIBLE_ADMIN) {
            // create a mock useraward so i can add it to the array without having to award the user
            // prevents users from having an admin award while not being admin
            $adminAward = new Award([
                'id' => 0,
                'user_id' => $this->id,
                'award_id' => 3
            ]);
            $relationship[] = $adminAward;
        }
        if (Carbon::parse($this->created_at)->addYear()->isPast()) {
            $classicAward = new Award([
                'id' => 0,
                'user_id' => $this->id,
                'award_id' => 1
            ]);
            $relationship[] = $classicAward;
        }
        // same thing for memberships
        $mem = $this->membership;
        if ($mem) {
            $memAward = new Award([
                'id' => 0,
                'user_id' => $this->id,
                'award_id' => $mem->membership + 4
            ]);
            $relationship[] = $memAward;
        }
        return $relationship;
    }

    public function getFeedAttribute()
    {
        // @phpstan-ignore-next-line
        return Status::where(function ($q) {
            $q->whereIn('owner_id', $this->hasMany(Friend::class, 'to_id', 'id')->isAccepted()->select('from_id'))
                ->orWhereIn('owner_id', $this->hasMany(Friend::class, 'from_id', 'id')->isAccepted()->select('to_id'));
        })->whereNull('clan_id')
            ->orWhereIn('clan_id', ClanMember::userId($this->id)->accepted()->select('clan_id'))
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->with(['user', 'clan'])
            ->get();
    }

    public function getVisitsAttribute()
    {
        return $this->sets()->sum('visits');
    }

    public function getViewedThreadsAttribute()
    {
        return
            $this->attributes['viewed_threads'] ??
            (array_slice(json_decode(Redis::get("user:{$this->id}:threads:views"), true) ?? [], 0, 50, true));
    }

    public function getViewedItemsAttribute()
    {
        return
            $this->attributes['viewed_items'] ??
            (json_decode(Cache::get("user" . $this->id . "shopViews"), true) ?? []);
    }

    public function getPostCountAttribute()
    {
        return Cache::remember('postCountUser' . $this->id, 1 * 60, function () {
            return $this->userThreads()->count() + $this->userPosts()->count();
        });
    }
}
