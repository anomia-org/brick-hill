<?php

namespace App\Traits\Models\Polymorphic;

use App\Traits\Models\Polymorphic;

use App\Models\Polymorphic\Asset;
use Illuminate\Database\Eloquent\Relations\{
    MorphOne,
    MorphMany
};

trait Assetable
{
    use Polymorphic;

    /**
     * Returns the latest asset related to the model
     * TODO: this needs to start using latestOfMany relation, original attempt didnt work properly for some reason and there are no seeders to test this as of now
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne<Asset>
     */
    public function latestAsset(): MorphOne
    {
        return $this->morphOne(Asset::class, 'assetable')
            ->where(fn ($query) => $query->where('is_selected_version', true)->orWhereNull('is_selected_version'))
            ->orderBy('id', 'DESC');
    }

    /**
     * Returns all asset versions related to the models
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Asset>
     */
    public function assets(): MorphMany
    {
        return $this->morphMany(Asset::class, 'assetable');
    }
}
