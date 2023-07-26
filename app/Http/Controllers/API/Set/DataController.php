<?php

namespace App\Http\Controllers\API\Set;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Set\Set;

use App\Http\Resources\Set\SetResource;

class DataController extends Controller
{
    /**
     * Returns information about a set
     * 
     * @param \App\Models\Set\Set $set 
     * @return \App\Http\Resources\Set\SetResource 
     */
    public function getSetData(Set $set)
    {
        return new SetResource($set->load('thumbnailAsset'));
    }

    /**
     * Returns information about passes for a set
     * 
     * @param \App\Models\Set\Set $set 
     * @return void 
     */
    public function getSetPasses(Set $set)
    {
        return;
    }
}
