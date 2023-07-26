<?php

namespace App\Http\Controllers\API\Thumbnails;

use App\Constants\Thumbnails\ThumbnailState;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Constants\Thumbnails\ThumbnailType;
use App\Contracts\Models\IThumbnailable;
use App\Exceptions\Custom\InvalidDataException;
use App\Http\Requests\General\BulkThumbnail;
use App\Jobs\Render\RenderThumbnail;

class ThumbnailController extends Controller
{
    use \App\Traits\Controllers\Polymorphic;

    /**
     * Allow multiple thumbnails to be requested in multiple HTTP post to improve load speeds
     * 
     * @param \App\Http\Requests\General\BulkThumbnail $request 
     * @param bool $bypassOverwrite 
     * @return \Illuminate\Support\Collection[] 
     */
    public function bulkThumbnails(BulkThumbnail $request, bool $bypassOverwrite = false)
    {
        $thumbCollection = collect($request->thumbnails);
        $thumbnailsByType = $thumbCollection->mapToGroups(function ($thumbnail) {
            return [$thumbnail['type'] => $thumbnail];
        });

        $data = $thumbCollection->mapWithKeys(fn ($thumb) => [$thumb['id'] . ":" . $thumb['type'] => []]);

        foreach ($thumbnailsByType as $typeId => $thumbnails) {
            $type = ThumbnailType::from((int) $typeId);

            $polyIds = $thumbnails->map(fn ($thumbnail) => $thumbnail['id']);

            $model = $this->retrieveModel('thumbnails', $type->morphType());
            $models = $model
                ->whereIn('id', $polyIds)
                ->with('thumbnails', fn ($q) => $q->notExpired()->wherePivot('thumbnail_type', $typeId))
                ->get();

            $models->each(function ($model) use ($data, $type, $bypassOverwrite) {
                $key = "$model->id:$type->value";
                $data->put($key, $this->modelToThumbnail($model, $type, $bypassOverwrite));
            });
        }

        $data = $data->map(fn ($val, $key) => [
            'key' => $key,
            ...$val
        ])->values();

        // could use a resource but those appear to only support models and we really arent return models here
        // althought i could flip this around entirely to have it return an actual Thumbnail model
        // but then i would have to store data inside that model on what its key is
        // hmmm lots to think about
        // ^ this wouldnt work as if a thumbnail isnt generated it wouldnt have a model,
        // i could make the resource for the parent model but at that point its easier to just handle an array
        return [
            'data' => $data
        ];
    }

    /**
     * Return a redirect to a thumbnail to allow for new thumbnail usage on pages that aren't written in vue
     * 
     * @param \Illuminate\Http\Request $request 
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse 
     */
    public function singleThumbnail(Request $request)
    {
        try {
            $type = ThumbnailType::from($request->type);
        } catch (\ValueError | \TypeError $e) {
            throw new InvalidDataException("Invalid ThumbnailType");
        }

        $model = $this->retrieveModel('thumbnails', $type->morphType());

        $thumbParent = $model->select()
            ->with('thumbnails', fn ($q) => $q->notExpired()->wherePivot('thumbnail_type', $type->value))
            ->findOrFail($request->id);

        $thumb = $this->modelToThumbnail($thumbParent, $type);

        $headers = [];

        if ($thumb['state'] === ThumbnailState::APPROVED->value || $thumb['state'] === ThumbnailState::DECLINED->value) {
            $headers['Cache-Control'] = 'public, max-age=300';
        }

        return redirect($thumb['thumbnail'])->withHeaders($headers);
    }

    /**
     * Read a Thumbnailables data to retrieve if its thumbnail is available and return it in a consistent format
     * 
     * @param \App\Contracts\Models\IThumbnailable $model 
     * @param \App\Constants\Thumbnails\ThumbnailType $type 
     * @param bool $bypassOverwrite 
     * @return array 
     */
    private function modelToThumbnail(IThumbnailable $model, ThumbnailType $type, bool $bypassOverwrite = false): array
    {
        $state = ThumbnailState::DECLINED;
        if ($model->overwriteThumbnailState() && !$bypassOverwrite) {
            $state = $model->overwriteThumbnailState();
        } else {
            if ($model->hasThumbnail()) {
                $thumb = $model->thumbnails->first();
                if (!is_null($thumb)) {
                    $state = ThumbnailState::APPROVED;
                } else {
                    RenderThumbnail::dispatch($model, $type);
                    $state = ThumbnailState::PENDING;
                }
            }
        }

        switch ($state) {
            case ThumbnailState::APPROVED:
                return ['thumbnail' => $type->url($thumb?->uuid), 'state' => $state->value];
            case ThumbnailState::PENDING:
                return ['thumbnail' => $type->pendingUrl(), 'state' => $state->value];
            case ThumbnailState::DECLINED:
                return ['thumbnail' => $type->declinedUrl(), 'state' => $state->value];
            case ThumbnailState::AWAITING_APPROVAL:
                return ['thumbnail' => $type->pendingUrl(), 'state' => $state->value];
                // TODO: should i even include this? should probably throw an internal error since its not a valid state?
                // TODO: does php have a way to ensure a switch checks for every type? i have heard of this existing in other languages
            default:
                return ['thumbnail' => $type->declinedUrl(), 'state' => $state->value];
        }
    }
}
