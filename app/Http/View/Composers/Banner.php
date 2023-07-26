<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;

use Illuminate\Support\Facades\Redis;

class Banner
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $banner = Redis::get("site_banner");
        if ($banner) {
            $view->with('site_banner', $banner);
            $view->with('site_banner_url', Redis::get("site_banner_url"));
        }
    }
}
