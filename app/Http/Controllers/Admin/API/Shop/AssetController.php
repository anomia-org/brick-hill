<?php

namespace App\Http\Controllers\Admin\API\Shop;

use App\Helpers\Assets\Creator;
use App\Helpers\Assets\Uploader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Http\Requests\Admin\Shop\UploadAsset;
use App\Http\Resources\Polymorphic\AssetResource;
use App\Models\Item\Item;
use App\Models\Polymorphic\Asset;
use App\Models\Polymorphic\AssetType;

class AssetController extends Controller
{
    /**
     * Upload an asset to the Brick Hill account
     * 
     * @param UploadAsset $request 
     * @param Uploader $uploader 
     * @return array 
     */
    public function uploadAsset(UploadAsset $request, Uploader $uploader): array
    {
        $type = AssetType::findOrFail($request->type);

        if ($request->type == 'image') {
            $asset = $uploader->upload($request->texture, $request->lossless === 'true');
        } else {
            $asset = $uploader->upload($request->mesh);
        }

        $asset->asset_type_id = $type->id;
        $asset->creator_id = config('site.main_account_id');
        $asset->is_approved = true;
        $asset->is_pending = false;
        $asset->save();

        return ['success' => true, 'asset_id' => $asset->id];
    }

    /**
     * Replaces an items asset with a new one given by JSON
     * 
     * @param Request $request 
     * @param Item $item 
     * @param Creator $creator 
     * @param Uploader $uploader 
     * @return array
     */
    public function updateAsset(Request $request, Item $item, Creator $creator, Uploader $uploader): array
    {
        if ($item->creator_id != config('site.main_account_id'))
            throw new \App\Exceptions\Custom\InvalidDataException('Only items created by Brick Hill can be updated');

        $data = json_decode($request->assetData);

        if (is_null($data))
            throw new \App\Exceptions\Custom\InvalidDataException("JSON is invalid");

        foreach ($data as $object) {
            $node = $creator->newNode($object->type);
            foreach ($object as $key => $value) {
                $node->setValue($key, $value);
            }
        }

        $itemAsset = $uploader->upload($creator->toJson());
        $item->assets()->update([
            'is_selected_version' => false
        ]);
        $itemAsset->asset_type_id = AssetType::type('asset')->firstOrFail()->id;
        $itemAsset->creator_id = config('site.main_account_id');
        $itemAsset->is_approved = true;
        $itemAsset->is_pending = false;
        $itemAsset->is_selected_version = true;
        $item->assets()->save($itemAsset);

        $item->thumbnails()->update([
            'expires_at' => now()
        ]);

        return ['success' => true];
    }

    /**
     * Get recent assets to be displayed in admin panel
     * 
     * @return \Illuminate\Http\Resources\Json\ResourceCollection<mixed>  
     */
    public function getRecentAssets()
    {
        return AssetResource::paginateCollection(Asset::where([['created_at', '>=', Carbon::now()->subDay()], ['creator_id', config('site.main_account_id')]])->paginateByCursor(['id' => 'desc']));
    }
}
