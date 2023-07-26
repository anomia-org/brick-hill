<?php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Set\Set;
use Illuminate\Support\Str;

use App\Http\Resources\Polymorphic\AssetResource;

class SetAPIController extends Controller
{
    public function getSetBrk(Request $request)
    {
        $set = Set::where('host_key', $request->host_key)->firstOrFail();

        if (!$set->is_dedicated)
            throw new \App\Exceptions\Custom\APIException("Server must be dedicated to retrieve brk");

        $asset = $set->bundledBrkAssets()->where(fn ($query) => $query->where('is_selected_version', true)->orWhereNull('is_selected_version'))->orderBy('id', 'DESC')->firstOrFail();

        return redirect(config('site.storage.domain') . "/v3/assets/$asset->private_uuid");
    }

    public function getSetAssets(Set $set)
    {
        return AssetResource::collection($set->bundledBrkAssets()->limit(5)->orderBy('id', 'DESC')->get());
    }

    public function getHostKey(Set $set)
    {
        if (is_null($set->host_key))
            $set->update([
                'host_key' => Str::random(64)
            ]);

        return [
            'success' => true,
            'key' => $set->host_key
        ];
    }

    public function newHostKey(Set $set)
    {
        $key = Str::random(64);
        $set->update([
            'host_key' => $key
        ]);

        return [
            'success' => true,
            'key' => $key
        ];
    }
}
