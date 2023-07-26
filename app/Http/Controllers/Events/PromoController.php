<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;

class PromoController extends Controller
{
    /**
     * Return view to display promo code input form
     * 
     * @return View|Factory 
     */
    public function displayPage()
    {
        return view('pages.events.promocode');
    }
}
