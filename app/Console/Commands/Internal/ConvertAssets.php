<?php

namespace App\Console\Commands\Internal;

use App\Jobs\ConvertAsset;
use App\Models\Clan\Clan;
use App\Models\Item\Item;
use App\Models\Item\ItemType;
use App\Models\Polymorphic\Asset;
use App\Models\Set\Set;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ConvertAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'internal:convert-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** @phpstan-ignore-next-line */
        Asset::whereHasMorph('assetable', [Item::class], function ($q) {
            $q->whereIn('type_id', [1, 2, 3, 4, 6, 7, 8]);
        })->with('assetable.itemType')->whereNull('new_format_uuid')->where('is_approved', 1)->chunkById(100, function ($assets) {
            foreach ($assets as $asset) {
                ConvertAsset::dispatch($asset, $asset->assetable->itemType);
            }
        });

        Asset::whereHasMorph('assetable', [Set::class])->whereNull('new_format_uuid')->chunkById(100, function ($assets) {
            foreach ($assets as $asset) {
                Storage::copy("/v2/assets/{$asset->private_uuid}", "/v3/assets/{$asset->private_uuid}");
                $asset->new_format_uuid = $asset->private_uuid;
                $asset->save();
            }
        });

        Asset::whereHasMorph('assetable', [Clan::class])->whereNull('new_format_uuid')->chunkById(100, function ($assets) {
            foreach ($assets as $asset) {
                Storage::copy("/v2/assets/{$asset->private_uuid}", "/v3/assets/{$asset->private_uuid}");
                $asset->new_format_uuid = $asset->private_uuid;
                $asset->save();
            }
        });

        Asset::where([['asset_type_id', 4], ['is_approved', 1]])->whereNull('new_format_uuid')->chunkById(100, function ($assets) {
            foreach ($assets as $asset) {
                Storage::copy("/v2/assets/{$asset->private_uuid}", "/v3/assets/{$asset->private_uuid}");
                $asset->new_format_uuid = $asset->private_uuid;
                $asset->save();
            }
        });

        return 0;
    }
}
