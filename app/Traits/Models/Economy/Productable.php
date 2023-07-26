<?php

namespace App\Traits\Models\Economy;

use Illuminate\Database\Eloquent\Relations\MorphOne;

use App\Traits\Models\Polymorphic;

use App\Models\Economy\Product;

trait Productable
{
    use Polymorphic, Crateable;

    /**
     * Returns if the model should be able to be purchased
     * 
     * @return bool 
     */
    abstract public function isPurchasable(): bool;

    /**
     * Returns the product related to the model
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne<Product>
     */
    public function product(): MorphOne
    {
        return $this->morphOne(Product::class, 'productable');
    }
}
