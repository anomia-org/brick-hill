<?php

namespace App\Jobs;

use App\Helpers\Assets\Optimizer;
use App\Helpers\Assets\Uploader;
use App\Models\Item\ItemType;
use App\Models\Polymorphic\Asset;

use AsyncAws\Lambda\LambdaClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ConvertAsset implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Asset to be converted
     * 
     * @var \App\Models\Polymorphic\Asset
     */
    protected Asset $asset;

    /**
     * Item type of the asset given
     * 
     * @var \App\Models\Item\ItemType
     */
    protected ItemType $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Asset $asset, ItemType $type)
    {
        $this->asset = $asset;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $optimizer = new Optimizer();
        $optimizer->setConstraint(836, 836);
        $uploader = new Uploader($optimizer, "v3");

        switch ($this->type->name) {
            case "shirt":
            case "tshirt":
            case "pants":
                $data = $this->invoke();

                $texAsset = Asset::findOrFail($data['texture_id']);

                // used incorrect conversion, must delete one one and resave
                if ($this->type->name == "tshirt" && !is_null($texAsset->new_format_uuid)) {
                    Storage::delete("/v3/assets/{$texAsset->new_format_uuid}");
                }

                $compressed = $optimizer->pngquant($data['texture']);
                $newAsset = $uploader->upload($compressed);

                Storage::copy("/v2/assets/{$this->asset->private_uuid}", "/v3/assets/{$this->asset->private_uuid}");

                $this->asset->new_format_uuid = $this->asset->uuid;
                $this->asset->save();

                $texAsset->new_format_uuid = $newAsset->uuid;
                $texAsset->save();
                break;
            case "face":
                $data = $this->invoke();

                $texAsset = Asset::findOrFail($data['texture_id']);

                if (!is_null($texAsset->new_format_uuid)) {
                    Storage::delete("/v3/assets/{$texAsset->new_format_uuid}");
                }

                $compressed = $optimizer->pngquant($data['texture']);
                $newAsset = $uploader->upload($compressed);

                Storage::copy("/v2/assets/{$this->asset->private_uuid}", "/v3/assets/{$this->asset->private_uuid}");

                $this->asset->new_format_uuid = $this->asset->uuid;
                $this->asset->save();

                $texAsset->new_format_uuid = $newAsset->uuid;
                $texAsset->save();
                break;
            case "hat":
            case "tool":
            case "head":
                $data = Http::get(config('site.storage.domain') . "/v2/assets/{$this->asset->private_uuid}")->json();

                $asset = $data[0];

                $assets = [$this->asset];

                if (array_key_exists("texture", $asset)) {
                    $id = str_replace("asset://", "", $asset["texture"]);
                    $tex = Asset::findOrFail($id);

                    $assets[] = $tex;
                }

                if (array_key_exists("mesh", $asset)) {
                    $mid = str_replace("asset://", "", $asset["mesh"]);
                    $mesh = Asset::findOrFail($mid);

                    $assets[] = $mesh;
                }

                // run in separate loops incase of error? idk bro
                foreach ($assets as $asset) {
                    Storage::copy("/v2/assets/{$asset->private_uuid}", "/v3/assets/{$asset->private_uuid}");
                }

                foreach ($assets as $asset) {
                    $asset->new_format_uuid = $asset->private_uuid;
                    $asset->save();
                }
                break;
        }
    }

    /**
     * Invoke Lambda with proper data and get return
     * 
     * @return array 
     */
    public function invoke(): array
    {
        $lambdaClient = new LambdaClient([
            'region' => 'us-east-1'
        ]);

        $callLambda = $lambdaClient->invoke([
            'FunctionName' => 'convert-assets',
            'Payload' => json_encode([
                'uuid' => $this->asset->private_uuid
            ])
        ]);

        $data = json_decode($callLambda->getPayload());

        return [
            'texture_id' => $data->texture_id,
            'texture' => $data->texture
        ];
    }
}
