<?php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Controls the flow of data from the site to the client
 * 
 * @package App\Http\Controllers\API\Client
 */
class DataController extends Controller
{
    /**
     * Returns all the Assets that the client should save locally
     * 
     * @return int[][] 
     */
    public function builtInAssets()
    {
        $skyboxes = [416649, 416652, 416653, 416654, 416655, 416656, 416662, 416663];

        return [
            'skyboxes' => $skyboxes
        ];
    }
}
