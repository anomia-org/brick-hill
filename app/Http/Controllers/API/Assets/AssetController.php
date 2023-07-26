<?php

namespace App\Http\Controllers\API\Assets;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

use App\Models\Polymorphic\Asset;

class AssetController extends Controller
{
    use \App\Traits\Controllers\Polymorphic;

    /**
     * Redirect to the file on the storage domain
     * 
     * @param Asset $asset 
     * @return Redirector|RedirectResponse 
     */
    public function getAsset(Asset $asset)
    {
        if (!$asset->is_approved)
            throw new \App\Exceptions\Custom\InvalidDataException("Asset is not approved");

        if ($asset->is_private)
            throw new \App\Exceptions\Custom\InvalidDataException("Asset is private");

        $version = "v2";

        // if the versions identical, send the new one as its not guaranteed a v2 will exist
        if ($asset->uuid === $asset->new_format_uuid) {
            $version = 'v3';
        }

        return redirect(config('site.storage.domain') . "/$version/assets/$asset->uuid");
    }

    /**
     * Redirect to an asset attached to a polymorphic relation
     * 
     * @param Request $request 
     * @return Redirector|RedirectResponse 
     */
    public function getPolyAsset(Request $request)
    {
        $class = $this->retrieveModel('latestAsset', $request->polymorphic_type);
        $assetable = $class->findOrFail($request->id);

        if (!$assetable->latestAsset?->is_approved)
            throw new \App\Exceptions\Custom\InvalidDataException("Asset is not approved");

        // TODO: phpstan things latestAsset isnt nullable -- why?
        // @phpstan-ignore-next-line
        if ($assetable->latestAsset?->is_private)
            throw new \App\Exceptions\Custom\InvalidDataException("Asset is private");

        $version = "v2";

        if ($assetable->latestAsset->uuid === $assetable->latestAsset->new_format_uuid) {
            $version = 'v3';
        }

        return redirect(config('site.storage.domain') . "/$version/assets/{$assetable->latestAsset->uuid}");
    }

    /**
     * Redirect to the file on the storage domain
     * 
     * @param Request $request 
     * @param Asset $asset 
     * @return Redirector|RedirectResponse 
     */
    public function getAssetv2(Request $request, Asset $asset)
    {
        // if not approved and not pending + is coming from renderer then throw error
        if (!$asset->is_approved) {
            if (!($asset->is_pending && $request->approval_key === "renderer")) {
                throw new \App\Exceptions\Custom\InvalidDataException("Asset is not approved");
            }
        }

        if ($asset->is_private)
            throw new \App\Exceptions\Custom\InvalidDataException("Asset is private");

        return redirect(config('site.storage.domain') . "/v3/assets/$asset->new_format_uuid");
    }

    /**
     * Redirect to an asset attached to a polymorphic relation
     * 
     * @param Request $request 
     * @return Redirector|RedirectResponse 
     */
    public function getPolyAssetv2(Request $request)
    {
        $class = $this->retrieveModel('latestAsset', $request->polymorphic_type);
        $assetable = $class->findOrFail($request->id);

        if (!$assetable->latestAsset?->is_approved)
            throw new \App\Exceptions\Custom\InvalidDataException("Asset is not approved");

        // TODO: phpstan things latestAsset isnt nullable -- why?
        // @phpstan-ignore-next-line
        if ($assetable->latestAsset?->is_private)
            throw new \App\Exceptions\Custom\InvalidDataException("Asset is private");

        return redirect(config('site.storage.domain') . "/v3/assets/{$assetable->latestAsset->new_format_uuid}");
    }
}
