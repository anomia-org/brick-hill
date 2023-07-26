<?php

namespace App\Console\Commands\Shop;

use App\Models\Item\ItemSchedule;
use Illuminate\Console\Command;

use Carbon\Carbon;

class CheckItemSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:schedules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for pending schedules and apply them';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $schedules = ItemSchedule::notCarriedOut()->past()->where('is_approved', true)->with('item')->get();

        foreach ($schedules as $schedule) {
            \DB::transaction(function () use ($schedule) {
                $schedule->carried_out = 1;
                $schedule->save();

                // there is basic handling when created but doesnt attempt to check for schedules beforehand
                // this is only fairly basic as i believe i can trust admins and their approvers to not to do too much wacky stuff
                // but everyone makes mistakes

                // if it attempts to make it not special
                if ($schedule->item->special && !$schedule->special) return;
                // if it attempts to make it not special_edition
                if ($schedule->item->special_edition && !$schedule->special_edition) return;
                // if it attempts to change the stock
                if ($schedule->item->special_q > 0 && $schedule->special_q != $schedule->item->special_q) return;

                $timestamps = [];

                // not sure if i like this method to hide it but i also dont want to attach yet another method to the item model
                if ($schedule->hide_update) {
                    $timestamps = [
                        'updated_at' => $schedule->item->updated_at,
                    ];
                }

                $schedule->item()->update([
                    'name' => $schedule->name,
                    'description' => $schedule->description,
                    // as these were added after the table was made and act in the future just check if they are null before making them replace any data
                    'type_id' => (is_null($schedule->type_id)) ? $schedule->item->type_id : $schedule->type_id,
                    'series_id' => $schedule->series_id,
                    'event_id' => $schedule->event_id,
                    'timer' => $schedule->timer,
                    'timer_date' => ($schedule->timer) ? $schedule->timer_date : Carbon::now(),
                    'special' => $schedule->special,
                    'special_edition' => $schedule->special_edition,
                    'special_q' => $schedule->special_q,
                    'is_public' => 1,
                    ...$timestamps
                ]);

                $schedule->item->product()->updateOrCreate([], [
                    'bucks' => $schedule->bucks,
                    'bits' => $schedule->bits,
                    'offsale' => is_null($schedule->bucks) && is_null($schedule->bits)
                ]);
            });

            $schedule->item->refresh();

            $schedule->item->searchable();
        }

        return Command::SUCCESS;
    }
}
