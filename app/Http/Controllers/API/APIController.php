<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class APIController extends Controller
{
    /**
     * Return a valid response to confirm that webserver is properly accessible
     * 
     * @return void 
     */
    public function albHealthcheck()
    {
        return;
    }
}
