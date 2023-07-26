<?php

namespace App\Http\Controllers\Admin\Super\API;

use App\Http\Controllers\Controller;
use App\Models\Membership\BillingProduct;
use App\Models\Set\ClientBuild;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redis;

/**
 * Controller for functions that change site-wide functions
 * 
 * @package App\Http\Controllers\Admin\Super\API
 */
class SiteController extends Controller
{
    /**
     * Adds a banner to the site
     * 
     * @param Request $request 
     * @return array 
     */
    public function siteBanner(Request $request)
    {
        Redis::set('site_banner', $request->banner);
        Redis::set('site_banner_url', $request->banner_url);
        return [
            'success' => true
        ];
    }

    /**
     * Enables maintenance mode on the site
     * 
     * @return array 
     */
    public function maintenanceMode()
    {
        return [
            'success' => Redis::set('maintenance', !Redis::get('maintenance'))
        ];
    }

    /**
     * Releases the new workshop
     * 
     * @return array 
     */
    public function workshopRelease()
    {
        $newStatus = !Redis::get('client_released');

        BillingProduct::where('name', 'Client Access')->update([
            'active' => $newStatus
        ]);

        ClientBuild::where([
            ['is_release', 1],
            ['id', '!=', ClientBuild::where('is_release', 1)->orderBy('id', 'DESC')->first()->id ?? 1],
        ])->update([
            'is_release' => 0
        ]);

        return [
            'success' => Redis::set('client_released', $newStatus)
        ];
    }
}
