<?php

namespace App\Constants\Thumbnails;

use App\Exceptions\Custom\Internal\InvalidDataException;
use App\Models\Item\Item;
use App\Models\Item\Version;
use App\Models\User\User;
use App\Models\User\Outfit;
use Illuminate\Database\Eloquent\Relations\Relation;

enum ThumbnailType: int
{
    case AVATAR_FULL = 1;
    case ITEM_FULL = 2;
    case OUTFIT_FULL = 3;
    case ITEM_VERSION_FULL = 4;

    /**
     * Returns the CDN URL of the image based on the type and the uuid
     * 
     * @param string $uuid 
     * @return string 
     */
    public function url(string $uuid): string
    {
        return config('site.storage.thumbnails') . "/" . $uuid;
    }

    /**
     * Returns the CDN URL of the pending image of the ThumbnailType
     * 
     * @return string 
     */
    public function pendingUrl(): string
    {
        return match ($this) {
            self::AVATAR_FULL => config('site.storage.pending.512'),
            self::ITEM_FULL => config('site.storage.pending.512'),
            self::OUTFIT_FULL => config('site.storage.pending.512'),
            self::ITEM_VERSION_FULL => config('site.storage.pending.512'),
        };
    }

    /**
     * Returns the CDN URL of the declined image of the ThumbnailType
     * 
     * @return string 
     */
    public function declinedUrl(): string
    {
        return match ($this) {
            self::AVATAR_FULL => config('site.storage.declined.512'),
            self::ITEM_FULL => config('site.storage.declined.512'),
            self::OUTFIT_FULL => config('site.storage.declined.512'),
            self::ITEM_VERSION_FULL => config('site.storage.declined.512'),
        };
    }

    /**
     * Returns the default size a Thumbnail should be rendered in
     * 
     * @return \App\Constants\Thumbnails\ThumbnailSize 
     */
    public function defaultSize(): ThumbnailSize
    {
        return match ($this) {
            self::AVATAR_FULL => ThumbnailSize::LARGE,
            self::ITEM_FULL => ThumbnailSize::ITEM_LARGE,
            self::OUTFIT_FULL => ThumbnailSize::MEDIUM,
            self::ITEM_VERSION_FULL => ThumbnailSize::SMALL,
        };
    }

    /**
     * Return the Model associated with the type of Thumbnail.
     * This isnt enforced in the database but should be accurate
     * 
     * @return string 
     */
    public function class(): string
    {
        return match ($this) {
            self::AVATAR_FULL => User::class,
            self::ITEM_FULL => Item::class,
            self::OUTFIT_FULL => Outfit::class,
            self::ITEM_VERSION_FULL => Version::class,
        };
    }

    /**
     * Return the polymorphic type of the Thumbnail, based on what class it should be associated with.
     * 
     * @return string 
     */
    public function morphType(): string
    {
        return array_search(self::class(), Relation::morphMap());
    }
}
