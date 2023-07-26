<?php

namespace App\Rules\Validation;

use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Database\Eloquent\Relations\Relation;

class PolyType implements ImplicitRule
{
    protected $type;
    protected $requiredMethod;

    /**
     * Receive the type attribute
     *
     * @param  string  $type
     * @param  mixed  $requiredMethod
     * @return bool
     */
    public function __construct($type, $requiredMethod = NULL) {
        $this->type = request($type);
        $this->requiredMethod = $requiredMethod;
    }

    /**
     * Determine if the validation rule passes.
     * Primary key of the model must be ID or it will fail
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $type = Relation::getMorphedModel($this->type);
        if(!$type || !class_exists($type))
            return false;
        if(!is_null($this->requiredMethod) && !method_exists($type, $this->requiredMethod))
            return false;
        
        return resolve($type)->where('id', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute must be a valid relation';
    }
}