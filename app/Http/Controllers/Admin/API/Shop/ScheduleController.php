<?php

namespace App\Http\Controllers\Admin\API\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use App\Models\Item\Item;
use App\Models\Item\ItemSchedule;

use App\Exceptions\Custom\InvalidDataException;

use App\Http\Requests\Admin\Shop\ScheduleItem;
use App\Http\Requests\General\Toggle;
use App\Http\Resources\Admin\Shop\ItemScheduleResource;
use App\Http\Resources\Item\ItemResource;

class ScheduleController extends Controller
{
    /**
     * Return ItemSchedules for use in admin panel
     * 
     * @param Request $request 
     * @return \Illuminate\Http\Resources\Json\ResourceCollection<mixed> 
     * @throws InvalidDataException 
     */
    public function scheduledItems(Request $request)
    {
        $query = match ($request->type) {
            'pending' => ItemSchedule::pendingApproval(),
            'upcoming' => ItemSchedule::upcoming()->where('is_approved', true),
            'past' => ItemSchedule::past(),
            default => throw new InvalidDataException
        };

        return ItemScheduleResource::paginateCollection($query->with('item', 'user', 'approver')->paginateByCursor(['scheduled_for' => 'desc', 'id' => 'desc']));
    }

    /**
     * Return all items that are not public or scheduled
     * 
     * @return \Illuminate\Http\Resources\Json\ResourceCollection<mixed>  
     */
    public function unscheduledItems()
    {
        return ItemResource::collection(Item::createdBy(config('site.main_account_id'))->where('is_public', 0)->whereDoesntHave('itemSchedule')->get());
    }

    /**
     * Schedule an item to be updated at a later time
     * 
     * @param ScheduleItem $request 
     * @return mixed
     * @throws InvalidDataException 
     */
    public function scheduleItem(ScheduleItem $request)
    {
        $item = Item::findOrFail($request->item['id']);

        $this->authorize('updateOfficial', $item);

        if ($item->creator_id != config('site.main_account_id'))
            throw new \App\Exceptions\Custom\InvalidDataException('Only items created by Brick Hill can be scheduled');

        if ($request->virtual['offsale']) {
            $bits = NULL;
            $bucks = NULL;
        } elseif ($request->virtual['free']) {
            $bits = 0;
            $bucks = 0;
        } else {
            $bits = $request->item['bits'];
            $bucks = $request->item['bucks'];
        }

        $stock = 0;
        if ((!$item->is_public && $request->item['special_edition']) || $item->special_edition || $item->special) {
            $stock = $request->item['stock'];

            // if its special it should already have a stock so dont overwrite it
            if ($item->special_edition || $item->special)
                $stock = $item->special_q;
        }

        ItemSchedule::create([
            'item_id' => $request->item['id'],
            'user_id' => Auth::id(),
            'name' => $request->item['name'],
            'description' => $request->item['description'],
            'type_id' => $request->item['type_id'],
            'series_id' => $request->item['series_id'] ?? NULL,
            'event_id' => $request->item['event']['id'] ?? NULL,
            'bits' => $bits,
            'bucks' => $bucks,
            'timer' => $request->item['timer'] && !$item->special,
            'timer_date' => $request->item['timer'] ? Carbon::parse($request->item['timer_date']) : NULL,
            'special' => ($item->is_public && $request->item['special']) || $item->special,
            'special_edition' => (!$item->is_public && $request->item['special_edition']) || $item->special_edition,
            'special_q' => $stock,
            'scheduled_for' => Carbon::parse($request->virtual['scheduled_for']),
            'hide_update' => $request->virtual['hide_update'],
        ]);

        return ['success' => true];
    }

    /**
     * Change approval status of an ItemSchedule
     * 
     * @param Toggle $request 
     * @param ItemSchedule $schedule 
     * @return mixed
     * @throws InvalidDataException 
     */
    public function approveSchedule(Toggle $request, ItemSchedule $schedule)
    {
        if (!Auth::user()->can('update', $schedule->approver))
            throw new \App\Exceptions\Custom\InvalidDataException('Higher level admin approved request');

        // already been denied, too complicated to handle cases to reapprove it
        if (!is_null($schedule->approver_id) && !$schedule->is_approved)
            throw new \App\Exceptions\Custom\InvalidDataException('Schedule has already been denied');

        // dont change status after it has already processed
        if ($schedule->is_approved && $schedule->scheduled_for->isPast())
            throw new \App\Exceptions\Custom\InvalidDataException('Schedule has already been processed');

        $schedule->is_approved = $request->toggle;
        $schedule->approver_id = Auth::id();

        $schedule->save();

        return [
            'success' => true
        ];
    }
}
