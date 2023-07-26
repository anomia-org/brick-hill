<?php

namespace App\Helpers\Assets;

use App\Exceptions\Custom\APIException;
use App\Exceptions\ValidationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{
    Log,
    Storage
};
use Ramsey\Uuid\Uuid;

use App\Helpers\Assets\Optimizer;

use App\Models\Polymorphic\Asset;

class Uploader
{
    /**
     * Namespace of all assets
     *
     * @var string
     */
    private const ASSET_NAMESPACE = "7de4d97c-b5e5-4c99-9186-3c8622296b90";

    /**
     * Optimizer to compress assets before upload
     *
     * @var Optimizer
     */
    public Optimizer $optimizer;

    /**
     * Version number to modify assets under
     * 
     * @var string
     */
    public string $storageVersion;

    /**
     * Creates new Uploader
     *
     * @param Optimizer $optimizer
     * @param string $storageVersion
     * 
     * @return void
     */
    public function __construct(Optimizer $optimizer, string $storageVersion = "v3")
    {
        $this->optimizer = $optimizer;
        $this->storageVersion = $storageVersion;
    }

    /**
     * Upload a file from request to S3,
     * returns a non-persisted Asset model
     * 
     * @param UploadedFile|string $file 
     * @param bool $skipOptimization
     * @return Asset
     *  
     * @throws APIException 
     */
    public function upload(UploadedFile|string $file, bool $skipOptimization = false): Asset
    {
        // string should only be passed in if the site is uploading a generated file
        if ($file instanceof UploadedFile) {
            if ($skipOptimization) {
                $file = $file->getContent();
            } else {
                $file = $this->optimizer->optimize($file);
            }
        }

        $uuid = $this->uuidFromFile($file);

        // package should automatically tag it with a Content-Type
        // in testing it seems to be accurate enough to work
        $success = Storage::put("/$this->storageVersion/assets/$uuid", $file);

        // if the upload fails success returns as false, but provides no data on the error
        // well designed
        if (!$success) {
            throw new APIException('Error uploading the file');
        }

        // it is up to the controller to persist it, attach it to the item, etc
        return new Asset(['uuid' => $uuid, 'new_format_uuid' => $uuid]);
    }

    public function delete(Asset $asset)
    {
        $check = Asset::where([['uuid', $asset->uuid], ['is_approved', 1], ['id', '!=', $asset->id]])->count();
        // cant delete an asset if there are still others that are approved
        if ($check > 0) {
            return false;
        }

        return Storage::delete("/$this->storageVersion/assets/{$asset->uuid}");
    }

    /**
     * Takes a string and returns uuid5 string in return,
     * namespaced based on self::ASSET_NAMESPACE
     *
     * @param  string $file
     * @return string
     */
    private function uuidFromFile(string $file): string
    {
        return Uuid::uuid5(self::ASSET_NAMESPACE, $file)->toString();
    }
}
