<?php

namespace App\Traits\Models\Economy;

use App\Traits\Models\Polymorphic;

use App\Models\User\Crate;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Crateable
{
    use Polymorphic;

    /**
     * Returns if the model should be able to be traded
     * 
     * @return bool 
     */
    abstract public function isTradeable(): bool;

    /**
     * Returns the crates related to the model
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Crate>
     */
    public function crates(): MorphMany
    {
        return $this->morphMany(Crate::class, 'crateable');
    }

    /**
     * Returns the owned crates related to the model
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Crate>
     */
    public function owners(): MorphMany
    {
        return $this->morphMany(Crate::class, 'crateable')->where('own', 1);
    }
}
