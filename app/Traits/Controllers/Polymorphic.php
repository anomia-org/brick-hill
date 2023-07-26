<?php

namespace App\Traits\Controllers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;

trait Polymorphic
{
    /**
     * Returns the class for a polymorphic request or returns a 404
     * TODO: this needs to be able to properly resolve type based on the incoming relation
     * 
     * @param string $relation 
     * @param string $morphed_type 
     * @return mixed
     */
    public function retrieveModel(string $relation, string $morphed_type)
    {
        $class = Relation::getMorphedModel($morphed_type);
        if (!class_exists($class) || !method_exists($class, $relation))
            abort(404);

        return resolve($class);
    }
}
