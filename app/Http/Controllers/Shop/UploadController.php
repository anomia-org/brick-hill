<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Helpers\{
    Assets\Uploader,
    Assets\Creator
};

use App\Models\Item\{
    Item,
    ItemType
};
use App\Models\Polymorphic\AssetType;
use App\Models\User\Crate;

use App\Http\Requests\Shop\{
    EditItem,
    Upload
};

class UploadController extends Controller
{
    use \App\Traits\Controllers\PostLimit;

    public function editPage(Item $item)
    {
        $this->authorize('update', $item);

        return view('pages.shop.edit')->with([
            'item' => $item
        ]);
    }

    public function saveItemEdit(EditItem $request, Item $item)
    {
        $this->authorize('update', $item);

        \DB::transaction(function () use ($request, $item) {
            $item->name = $request->title;
            $item->description = $request->description;
            $item->save();

            if ($request->offsale) {
                $item->product()->updateOrCreate([], ['offsale' => true]);
            } elseif ($request->free) {
                $item->product()->updateOrCreate([], [
                    'bucks' => 0,
                    'bits' => 0,
                    'offsale' => false
                ]);
            } else {
                $item->product()->updateOrCreate([], [
                    'bucks' => $request->bucks,
                    'bits' => $request->bits,
                    'offsale' => false
                ]);
            }
        });

        return;
    }

    public function uploadItem(Upload $request, Uploader $uploader, Creator $creator)
    {
        if (!Carbon::parse(Auth::user()->created_at)->addDays(3)->isPast())
            throw new \App\Exceptions\Custom\APIException('Your account must be at least three days old to make items');

        if (!$this->canMakeNewPost(Auth::user()->items(), 60))
            throw new \App\Exceptions\Custom\APIException('You can only create one item every 60 seconds');

        $uploader->optimizer->setConstraint(836, 836);

        $newItem = Item::create([
            'creator_id' => Auth::id(),
            'name' => $request->title,
            'description' => $request->description,
            'is_approved' => 0,
            'is_pending' => 0,
            'type_id' => ItemType::type($request->type)->firstOrFail()->id,
            'timer' => 0, // TODO: this value needs a default too
            'timer_date' => Carbon::now(), // TODO: allow nullable timer_data, clean up item table overall
            'special_edition' => 0, // TODO: probably these too
            'special_q' => 0
        ]);

        $asset = $uploader->upload($request->file);
        $asset->asset_type_id = AssetType::type('image')->firstOrFail()->id;
        $asset->creator_id = Auth::id();
        $asset->is_approved = true;
        $asset->is_pending = false;
        $asset->save();

        $creator->newNode($request->type)->setValue('texture', "asset://{$asset->id}");
        $itemAsset = $uploader->upload($creator->toJson());
        $itemAsset->asset_type_id = AssetType::type('asset')->firstOrFail()->id;
        $itemAsset->creator_id = Auth::id();
        $newItem->assets()->save($itemAsset);

        $newItem->is_pending = true;
        $newItem->save();

        Crate::create([
            'user_id' => Auth::id(),
            'crateable_id' => $newItem->id,
            'crateable_type' => 1,
            'serial' => 1
        ]);

        return [
            'success' => $newItem->id
        ];
    }
}
