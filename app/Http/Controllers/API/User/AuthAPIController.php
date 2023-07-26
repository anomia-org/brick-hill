<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthAPIController extends Controller
{
    /**
     * Return users information through OAuth
     * 
     * @return array 
     */
    public function userInformation()
    {
        return [
            'user' => Auth::user()->only(['username', 'id']),
            'is_beta_tester' => Auth::user()->is_beta_tester,
            'has_client_access' => Auth::user()->clientAccess()->exists() || Auth::user()->is_beta_tester
        ];
    }
}
