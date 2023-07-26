<?php

namespace App\Jobs\Render;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\Middleware\ThrottlesExceptionsWithRedis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

use AsyncAws\Lambda\LambdaClient;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

use App\Helpers\Assets\Optimizer;
use App\Constants\Thumbnails\ThumbnailType;
use App\Contracts\Models\IThumbnailable;
use App\Exceptions\Custom\APIException;
use App\Exceptions\Custom\Internal\InvalidDataException;
use App\Models\Polymorphic\Thumbnail;
use App\Models\Item\Item;

class RenderThumbnail implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Namespace of all thumbnail contents
     *
     * @var string
     */
    private const THUMBNAIL_CONTENTS_NAMESPACE = "f0e256da-9f8e-4176-b636-01562b50b4ce";

    /**
     * Model to attach the Thumbnail to
     * 
     * @var \App\Contracts\Models\IThumbnailable
     */
    protected IThumbnailable $model;

    /**
     * Type of Thumbnail to generate
     * 
     * @var \App\Constants\Thumbnails\ThumbnailType
     */
    protected ThumbnailType $type;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 900;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->model->id . ":" . $this->type->value;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        // if the job fails twice, wait 3 minutes before trying again
        // if the job fails once, wait 1 minute before trying again
        return [(new ThrottlesExceptionsWithRedis(2, 3))->backoff(1)];
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addMinutes(5);
    }

    /**
     * Create a new job instance.
     * @param \App\Contracts\Models\IThumbnailable $model 
     * @param \App\Constants\Thumbnails\ThumbnailType $type 
     * @return void 
     */
    public function __construct(IThumbnailable $model, ThumbnailType $type)
    {
        $this->model = $model;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Optimizer $optimizer)
    {
        $outfit = $this->getOutfit();

        // thumbnail model itself has no idea what the type is, so must be stored in contents_uuid to differentiate between the types
        $contentsUuid = $this->uuidFromInput("{$this->type->value}_{$outfit}");

        $existingThumb = $this->checkForExisting($contentsUuid);
        if (!is_null($existingThumb)) {
            $this->replaceThumbnail($existingThumb);
            return;
        }

        // imagine the lambda was called and we got an image returned here
        $data = $this->invoke($outfit);

        $optimized = $optimizer->pngquant(base64_decode($data['image']));

        $disk = Storage::disk('s3-thumbnails');

        $success = $disk->put("v1/" . $data['uuid'], $optimized);

        // if it didnt save we dont want to make a thumbnail for it
        if (!$success) {
            throw new APIException("Thumbnail failed to save");
        }

        $thumb = Thumbnail::create([
            'uuid' => $data['uuid'],
            'contents_uuid' => $contentsUuid,
            'expires_at' => Carbon::now()->addYear(),
        ]);
        $this->replaceThumbnail($thumb);

        return;
    }

    /**
     * Invoke Lambda with proper data and get return
     * 
     * @return array 
     */
    public function invoke(string $outfit): array
    {
        if (App::environment(['local', 'testing']))
            return [
                'image' => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAEUlEQVR4nGKalKh7ABAAAP//BJQB45OLdzgAAAAASUVORK5CYII=',
                'uuid' => $this::THUMBNAIL_CONTENTS_NAMESPACE
            ];

        $lambdaClient = new LambdaClient([
            'region' => 'us-east-1'
        ]);

        $callLambda = $lambdaClient->invoke([
            'FunctionName' => 'renderer-client-based',
            'Payload' => json_encode([
                'AvatarJSON' => $outfit,
                'Size' => $this->type->defaultSize()->value
            ])
        ]);

        $data = json_decode($callLambda->getPayload());

        if (!property_exists($data, "Image")) {
            // throw an InvalidDataException as this shouldnt happen unless the outfit is broken or the site is down
            // either way i want data on why it did it
            throw new InvalidDataException("Lambda failed to generate render on outfit: " . $outfit);
        }

        return [
            'image' => $data->Image,
            'uuid' => $data->UUID
        ];
    }

    /**
     * Determines if the Thumbnail already exists in the database and represents the most recent version of asset
     * 
     * @param string $contentsUuid 
     * @return null|\App\Models\Polymorphic\Thumbnail 
     */
    private function checkForExisting(string $contentsUuid): ?Thumbnail
    {
        $thumbnail = Thumbnail::where('contents_uuid', $contentsUuid)->notExpired()->first();

        if (is_null($thumbnail)) {
            return null;
        }

        $hasRecentlyUpdatedItem = Item::whereIn('id', $this->model->items_list)
            ->whereHas('latestAsset', fn ($q) => $q->where('updated_at', '>=', $thumbnail->created_at))->exists();

        if ($hasRecentlyUpdatedItem) {
            $thumbnail->update([
                'expires_at' => now()
            ]);
            return null;
        } else {
            return $thumbnail;
        }
    }

    /**
     * Replace the Thumbnailables attached thumbnail with the newly generated one
     * 
     * @param \App\Models\Polymorphic\Thumbnail $newThumb 
     * @return void 
     */
    private function replaceThumbnail(Thumbnail $newThumb)
    {
        // detach values with current thumbnail_type
        $this->model->thumbnails()->wherePivot('thumbnail_type', $this->type->value)->detach();

        // attach the new model
        $this->model->thumbnails()->attach($newThumb, ['thumbnail_type' => $this->type]);
    }

    /**
     * Returns the outfit data based on the incoming model
     * 
     * @return string 
     */
    private function getOutfit(): string
    {
        $data = $this->model->getThumbnailData();
        // exporter conforms to retrieveAvatar api which requires user_id
        $data['user_id'] = 1;

        if (is_array($data['items'])) {
            if (!array_key_exists('clothing', $data['items'])) {
                $data['items']['clothing'] = [];
            }
        } else {
            if (!$data['items']->has('clothing')) {
                $data['items']->put('clothing', []);
            }
        }

        return json_encode($data);
    }

    /**
     * Returns a Thumbnail contents_uuid based on the input
     *
     * @param  string $input
     * @return string
     */
    private function uuidFromInput(string $input): string
    {
        return Uuid::uuid5(self::THUMBNAIL_CONTENTS_NAMESPACE, $input)->toString();
    }
}
