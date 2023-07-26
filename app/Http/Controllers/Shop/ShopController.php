<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;

class ShopController extends Controller
{
    public function index()
    {
        return view('pages.shop.shop');
    }
}
