<?php

namespace App\Traits\Models;

trait Polymorphic
{
    /**
     * Boots the relationship and makes sure it has a valid morph map
     *
     * @return void
     */
    final public static function bootPolymorphicRelationship()
    {
        if ((new self)->getMorphClass() === static::class) {
            throw new \App\Exceptions\Custom\Internal\InvalidDataException('Relation morph not mapped for ' . static::class);
        }
    }
}
