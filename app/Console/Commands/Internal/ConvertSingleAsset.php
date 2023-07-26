<?php

namespace App\Console\Commands\Internal;

use App\Jobs\ConvertAsset;
use App\Models\Polymorphic\Asset;
use Illuminate\Console\Command;

class ConvertSingleAsset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'internal:convert-single {asset}';

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
        $asset = Asset::findOrFail($this->argument('asset'));

        /** @phpstan-ignore-next-line */
        ConvertAsset::dispatch($asset, $asset->assetable->itemType);

        return 0;
    }
}
