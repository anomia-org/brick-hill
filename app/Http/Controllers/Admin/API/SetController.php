<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\General\Toggle;
use App\Models\Set\Set;
use Illuminate\Http\Request;

class SetController extends Controller
{
    /**
     * Toggle featured status of a given set.
     * 
     * @param \App\Http\Requests\General\Toggle $request 
     * @param \App\Models\Set\Set $set 
     * @return true[] 
     */
    public function toggleFeaturedStatus(Toggle $request, Set $set)
    {
        $set->timestamps = false;
        $set->is_featured = $request->toggle;
        $set->save();

        return [
            'success' => true
        ];
    }

    /**
     * Toggle scrubbed status of name
     * 
     * @param \App\Models\Set\Set $set 
     * @return void 
     */
    public function scrubName(Set $set)
    {
        $set->is_name_scrubbed = !$set->is_name_scrubbed;
        $set->save();

        return;
    }

    /**
     * Toggle scrubbed status of description
     * 
     * @param \App\Models\Set\Set $set 
     * @return void 
     */
    public function scrubDesc(Set $set)
    {
        $set->is_description_scrubbed = !$set->is_description_scrubbed;
        $set->save();

        return;
    }
}
