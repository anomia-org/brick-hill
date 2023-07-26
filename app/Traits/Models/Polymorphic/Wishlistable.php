<?php

namespace App\Traits\Models\Polymorphic;

use App\Traits\Models\Polymorphic;

use App\Models\Polymorphic\Wishlist;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Wishlistable
{
    use Polymorphic;

    /**
     * Returns if the Wishlistable can be wishlisted
     * 
     * @return bool 
     */
    abstract public function isWishlistable(): bool;

    /**
     * Returns wishlists related to the model
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Wishlist>
     */
    public function wishlists(): MorphMany
    {
        return $this->morphMany(Wishlist::class, 'wishlistable');
    }
}
