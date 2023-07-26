<?php

namespace App\Http\Controllers\Admin\Super;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PanelController extends Controller
{
    public function home() {
        return view('pages.admin.super.main');
    }
}
