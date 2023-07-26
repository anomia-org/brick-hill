<?php

namespace App\Http\Controllers\Admin\API\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shop\UploadItem;

use App\Helpers\Assets\Creator;
use App\Helpers\Assets\Uploader;

use App\Models\Item\Item;
use App\Models\Item\ItemType;
use App\Models\Polymorphic\AssetType;

use App\Exceptions\Custom\APIException;
use App\Http\Requests\Admin\Shop\CreateEvent;
use App\Http\Resources\Item\EventResource;
use App\Models\Item\Event;

class ItemController extends Controller
{
    /**
     * Upload an item to the Brick Hill account
     * 
     * @param UploadItem $request 
     * @param Uploader $uploader 
     * @param Creator $creator 
     * @return array
     * 
     * @throws APIException 
     */
    public function uploadItem(UploadItem $request, Uploader $uploader, Creator $creator): array
    {
        $newItem = Item::create([
            'creator_id' => config('site.main_account_id'),
            'name' => $request->title,
            'type_id' => ItemType::type($request->type)->firstOrFail()->id,
            'is_public' => false,
            'is_approved' => true,
            'is_pending' => false,
            'timer' => false
        ]);

        $itemNode = $creator->newNode($request->type);
        if ($request->has('texture')) {
            $asset = $uploader->upload($request->texture);
            $asset->asset_type_id = AssetType::type('image')->firstOrFail()->id;
            $asset->creator_id = config('site.main_account_id');
            $asset->is_approved = true;
            $asset->is_pending = false;
            $asset->save();

            $itemNode->setValue('texture', "asset://{$asset->id}");
        }

        if ($request->has('mesh')) {
            $asset = $uploader->upload($request->mesh);
            $asset->asset_type_id = AssetType::type('mesh')->firstOrFail()->id;
            $asset->creator_id = config('site.main_account_id');
            $asset->is_approved = true;
            $asset->is_pending = false;
            $asset->save();

            $itemNode->setValue('mesh', "asset://{$asset->id}");
        }

        $itemAsset = $uploader->upload($creator->toJson());
        $itemAsset->asset_type_id = AssetType::type('asset')->firstOrFail()->id;
        $itemAsset->creator_id = config('site.main_account_id');
        $itemAsset->is_approved = true;
        $itemAsset->is_pending = false;
        $newItem->assets()->save($itemAsset);

        $newItem->thumbnails()->update([
            'expires_at' => now()
        ]);

        return ['success' => true];
    }

    /**
     * Create an Event with the given data
     * 
     * @param \App\Http\Requests\Admin\Shop\CreateEvent $request 
     * @return void 
     */
    public function createEvent(CreateEvent $request)
    {
        Event::create([
            'name' => $request->name,
            'start_date' => $request->date('start_date'),
            'end_date' => $request->date('end_date')
        ]);

        return;
    }

    /**
     * Modify an Event with the given data
     * 
     * @param \App\Http\Requests\Admin\Shop\CreateEvent $request 
     * @param \App\Models\Item\Event $event 
     * @return void 
     */
    public function modifyEvent(CreateEvent $request, Event $event)
    {
        $event->name = $request->name;
        $event->start_date = $request->date('start_date');
        $event->end_date = $request->date('end_date');

        $event->save();

        return;
    }

    /**
     * Return available Events
     * 
     * @return mixed 
     */
    public function listEvents()
    {
        return EventResource::paginateCollection(Event::paginateByCursor(['id' => 'desc']));
    }
}
