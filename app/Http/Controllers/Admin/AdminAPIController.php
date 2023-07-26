<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User\{
    User,
    Admin\Report
};
use App\Models\Item\Item;
use App\Models\Clan\Clan;

use App\Http\Resources\Polymorphic\AssetResource;
use App\Http\Resources\Admin\{
    ReportResource,
    PendingItemResource,
};
use App\Models\Polymorphic\Asset;

class AdminAPIController extends Controller
{
    public function pendingItems()
    {
        return PendingItemResource::collection(Item::pending()->limit(10)->with('latestAsset')->get());
    }

    public function pendingAssets()
    {
        return AssetResource::collection(Asset::where([['is_pending', 1], ['assetable_type', '!=', 1]])->limit(10)->get());
    }

    public function reports()
    {
        return ReportResource::collection(Report::unseen()->with('sender:id,username,avatar_hash', 'reportable', 'report_reason')->get());
    }

    public function pointsLeaderboard()
    {
        return User::where([['admin_points', '>', 0], ['power', '>', 0]])
            ->orderBy('admin_points', 'DESC')
            ->get(['id', 'username', 'admin_points']);
    }
}
