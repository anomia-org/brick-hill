<?php

namespace App\Traits\Models\User\Admin;

use App\Traits\Models\Polymorphic;

use App\Models\User\Admin\Report;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Reportable
{
    use Polymorphic;

    /**
     * Returns the url to view the model
     * 
     * Might want to integrate this into a general polymorphic class as its not only relatable to a reportable item
     * 
     * @return string 
     */
    abstract public function getModelUrlAttribute(): string;

    /**
     * Due to bad DB design or something the tables have lots of different column names for the creator of the asset.
     * This is here to unify those IDs to allow for the frontend to be able to easily know who should be banned for creating something.
     * 
     * @return int 
     */
    abstract public function getModelAuthorAttribute(): int;

    /**
     * Returns a full combination of all potentially reportable data is stored in the raw DB row, files will need to be seen by visiting the url
     * 
     * @return string 
     */
    abstract public function getReportableContentAttribute(): string;

    /**
     * Returns a url to the image of the attribute or null
     * 
     * @return string 
     */
    abstract public function getReportableImageAttribute(): string|null;

    /**
     * Returns reports related to the model
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Report> 
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
