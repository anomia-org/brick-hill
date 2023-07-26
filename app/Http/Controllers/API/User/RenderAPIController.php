<?php

namespace App\Http\Controllers\API\User;

use App\Constants\Thumbnails\ThumbnailType;
use App\Exceptions\Custom\APIException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Http\Requests\User\Customize\Render;
use App\Http\Requests\User\Customize\CreateOutfit;

use App\Jobs\Render\RenderThumbnail;
use App\Models\Item\Item;
use App\Models\User\User;
use App\Models\User\Outfit;

use App\Helpers\Event;

class RenderAPIController extends Controller
{
    use \App\Traits\Controllers\PostLimit;

    /**
     * Takes instructions and applies them the the Avatar
     * Returns a thumbnail of the user
     * 
     * @param \App\Http\Requests\User\Customize\Render $request 
     * @return array 
     */
    public function renderProcess(Render $request, Event $event): array
    {
        $avatar = Auth::user()->avatar;

        if (!$avatar)
            throw new APIException("No avatar found for user");

        if ($request->rebase) {
            $avatar->items = collect([
                'hats' => [0, 0, 0, 0, 0],
                'face' => 0,
                'tool' => 0,
                'head' => 0,
                'figure' => 0,
                'shirt' => 0,
                'pants' => 0,
                'tshirt' => 0,
                'clothing' => []
            ]);

            $avatar->colors = collect([
                'head' => 'f3b700',
                'torso' => '85ad00',
                'right_arm' => 'f3b700',
                'left_arm' => 'f3b700',
                'right_leg' => '76603f',
                'left_leg' => '76603f'
            ]);
        }

        // we need to convert old avatars to the new format, this is basically what it converts to
        if (!$avatar->items->has('clothing')) {
            $convClothing = [];

            if (($pants = $avatar->items->get('pants')) > 0) {
                $convClothing[] = $pants;
            }

            if (($shirt = $avatar->items->get('shirt')) > 0) {
                $convClothing[] = $shirt;
            }

            if (($tshirt = $avatar->items->get('tshirt')) > 0) {
                $convClothing[] = $tshirt;
            }

            $avatar->items->put('clothing', $convClothing);
        }

        if ($request->has('colors')) {
            foreach ($request->colors as $part => $color) {
                // $fillable in Avatar model insures that even if an invalid part gets through validation it wont be send to the db
                $avatar->colors->put($part, strtolower($color));
            }
        }

        if ($request->has('instructions')) {
            $wearing = array_filter($request->instructions, fn ($val) => $val['type'] == 'wear');
            foreach ($request->instructions as $key => $instruction) {
                $value = $instruction['value'];

                $items = Item::select('id', 'type_id')
                    ->whereIn('id', collect($wearing)->pluck('value'))
                    ->with('itemType')
                    ->get();

                switch ($instruction['type']) {
                    case "wearOutfit":
                        /** @var Outfit */
                        $outfit = Outfit::findOrFail($value);

                        $avatar->items = $outfit->items;
                        $avatar->colors = $outfit->colors;

                        // verify that the user owns all the items in the new Avatar
                        $itemIds = $avatar->items_list;
                        $itemCount = Auth::user()
                            ->crate()
                            ->whereIn('crateable_id', $itemIds)
                            ->where('crateable_type', 1)
                            ->distinct('crateable_id', 'crateable_type')
                            ->count();
                        if ($itemCount < count($itemIds))
                            throw new APIException("You must own all items in the outfit");
                        break;
                    case "wear":
                        $item = $items->find($value);

                        if ($item->itemType->name == 'hat') {
                            if (in_array($value, $avatar->items->get('hats', [])))
                                break;
                            $index = array_search(0, $avatar->items->get('hats', []));
                            if ($index === false) $index = 0;

                            $new = $avatar->items->get('hats');
                            $new[$index] = $value;
                            $avatar->items->put('hats', $new);
                        } else if (in_array($item->itemType->name, ['pants', 'shirt', 'tshirt'])) {
                            if (!$avatar->items->has('clothing')) {
                                $avatar->items->put('clothing', []);
                            }

                            if (in_array($value, $avatar->items->get('clothing', [])))
                                break;

                            $new = $avatar->items->get('clothing', []);
                            // limit layers to 5
                            if (count($new) == 5) {
                                $new[4] = $value;
                            } else {
                                $new[] = $value;
                            }
                            $avatar->items->put('clothing', $new);
                        } else {
                            $avatar->items->put($item->itemType->name, $value);
                        }
                        break;
                    case "remove":
                        // we dont really need to verify that the item exists or what type it is
                        // simply just remove all values associated with it
                        $avatar->items = $avatar->items->map(function ($val, $key) use ($value) {
                            if (is_array($val)) {
                                $index = array_search($value, $val);
                                if ($index !== false) {
                                    // hats have a forced array length of 5 due to legacy issues (?)
                                    // newer features shouldnt so unset the value
                                    if ($key == 'hats') {
                                        $val[$index] = 0;
                                    } else {
                                        array_splice($val, $index, 1);
                                    }
                                }
                                return $val;
                            }
                            return $val == $value ? 0 : $val;
                        });
                        break;
                    case "reorderClothing":
                        if (!$avatar->items->has('clothing')) {
                            $avatar->items->put('clothing', []);
                        }

                        if (!is_array($value) || array_diff($value, $avatar->items->get('clothing')))
                            break;

                        $avatar->items->put('clothing', array_values($value));
                        break;
                }
            }
        }

        // $avatar->isDirty() detects changes in JSON even when nothing actually changed
        $isDirty =
            $avatar->colors != $avatar->getOriginal()['colors']
            || $avatar->items != $avatar->getOriginal()['items'];

        if ($isDirty) {
            $avatar->save();
            $this->renderAvatar(Auth::user());
        }

        return [
            'success' => true
        ];
    }

    /**
     * Creates an Outfit off the users currently stored Avatar
     * 
     * @param \App\Http\Requests\User\Customize\CreateOutfit $request 
     * @return void 
     */
    public function createOutfit(CreateOutfit $request): void
    {
        if (!$this->canMakeNewPost(Auth::user()->outfits(), 30))
            throw new APIException('You can only create one outfit every 30 seconds');

        $outfitCount = Outfit::active()->userId(Auth::id())->count();
        if ($outfitCount >= 40)
            throw new APIException("You can only have 40 outfits");

        $avatar = Auth::user()->avatar;
        if (!$avatar)
            throw new APIException("User does not have an avatar");

        Outfit::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
            'uuid' => Str::uuid(),
            'items' => $avatar->items,
            'variations' => $avatar->variations,
            'colors' => $avatar->colors
        ]);
    }

    /**
     * Change the name of an Outfit
     * 
     * @param \App\Http\Requests\User\Customize\CreateOutfit $request 
     * @param \App\Models\User\Outfit $outfit 
     * @return void 
     */
    public function renameOutfit(CreateOutfit $request, Outfit $outfit): void
    {
        $this->authorize('update', $outfit);

        $outfit->name = $request->name;
        $outfit->save();
    }

    /**
     * Replace an Outfit with a Users current Avatar
     * 
     * @param \App\Models\User\Outfit $outfit 
     * @return void 
     */
    public function changeOutfit(Outfit $outfit): void
    {
        $this->authorize('update', $outfit);

        $avatar = Auth::user()->avatar;

        if ($avatar->items == $outfit->items && $avatar->colors == $outfit->colors)
            throw new APIException("Currently wearing same outfit");

        $outfit->items = $avatar->items;
        $outfit->variations = $avatar->variations;
        $outfit->colors = $avatar->colors;
        $outfit->uuid = Str::uuid();
        $outfit->save();

        $outfit->thumbnails()->detach();
    }

    /**
     * Delete an Outfit
     * 
     * @param \App\Models\User\Outfit $outfit 
     * @return void 
     */
    public function deleteOutfit(Outfit $outfit): void
    {
        $this->authorize('delete', $outfit);

        $outfit->active = 0;
        $outfit->save();
    }

    /**
     * Render the Avatar and return the thumbnail hash
     * 
     * @param \App\Models\User\User $user 
     * @return void 
     */
    private function renderAvatar(User $user): void
    {
        if (App::environment(['local', 'testing'])) {
            // sleep to recreate accurate latency of the api :troll:
            // sleep(1);
            return;
        }

        $user->thumbnails()->detach();

        RenderThumbnail::dispatch($user, ThumbnailType::AVATAR_FULL);

        return;
    }
}
