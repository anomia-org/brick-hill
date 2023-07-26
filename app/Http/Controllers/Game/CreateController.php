<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use AsyncAws\Lambda\LambdaClient;

use App\Models\Set\Set;
use App\Models\Polymorphic\AssetType;

use App\Helpers\Assets\Uploader;

use App\Http\Requests\Game\Upload\{
    CreateSet,
    UploadBrk,
    UploadThumbnail,
};
use PharData;

class CreateController extends Controller
{
    use \App\Traits\Controllers\PostLimit;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createSet()
    {
        if (!Carbon::parse(Auth::user()->created_at)->addDays(3)->isPast())
            return error('Your account must be at least three days old to make sets');

        $setCount = Set::where('creator_id', Auth::id())
            ->count();
        $max = auth()->user()->membership_limits->sets;

        if ($setCount + 1 > $max)
            return redirect()
                ->route('mySets')
                ->withErrors(['errors' => 'Max set limit reached']);

        return view('pages.sets.create');
    }

    public function createSetPost(CreateSet $request)
    {
        $setCount = Set::where('creator_id', Auth::id())
            ->count();
        $max = Auth::user()->membership_limits->sets;

        if (!Carbon::parse(Auth::user()->created_at)->addDays(3)->isPast())
            return error('Your account must be at least three days old to make sets');

        if ($setCount + 1 > $max)
            return redirect()
                ->route('mySets')
                ->withErrors(['errors' => 'Max set limit reached']);

        $set = Set::create([
            'creator_id' => Auth::id(),
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'uid' => ''
        ]);

        return redirect()
            ->route('set', ['id' => $set->id]);
    }

    public function uploadBrk(UploadBrk $request, Set $set, Uploader $uploader)
    {
        if (!$set->is_dedicated)
            throw new \App\Exceptions\Custom\APIException('Set must be dedicated to upload');

        if (!$this->canMakeNewPost($set->assets()->where('asset_type_id', 4), 60))
            throw new \App\Exceptions\Custom\APIException('You can only change the set once every 60 seconds');

        $client = new LambdaClient([
            'region' => 'us-east-1'
        ]);

        if ($request->brk->guessExtension() == "bin") {
            throw new \App\Exceptions\Custom\InvalidDataException("Binary file are not supported. Only legacy workshop maps can be uploaded.");
        }

        $lambdaReturn = $client->invoke([
            'FunctionName' => 'brk-bundler',
            'Payload' => json_encode([
                'name' => $request->brk->getClientOriginalName(),
                'brk' => $request->brk->get(),
                'scripts' => collect($request->scripts)->map(fn ($file, $k) => [
                    'name' => $file->getClientOriginalName(),
                    'content' => $file->get()
                ])
            ])
        ]);

        $jsonContents = json_decode($lambdaReturn->getPayload());
        if (empty($jsonContents->bbrk)) {
            throw new \App\Exceptions\Custom\APIException("Error saving brk. Make sure it is a legacy workshop map and that all JS files are valid.");
        }

        $contents = $jsonContents->bbrk;

        $file = tempnam(sys_get_temp_dir(), "bbrk");
        $tar_file = $file . ".tar";
        $tar = new PharData($tar_file);
        $tar->addFromString('set.bbrk', base64_decode($contents));

        $tar_contents = file_get_contents($tar_file);

        unlink($file);
        unlink($tar_file);

        $asset = $uploader->upload($tar_contents, true);
        $asset->asset_type_id = AssetType::type('bundled_brk')->firstOrFail()->id;
        $asset->creator_id = Auth::id();
        $asset->is_selected_version = true;
        $asset->is_pending = false;
        $asset->is_approved = true;
        $asset->is_private = true;

        $set->bundledBrkAssets()->update([
            'is_selected_version' => 0
        ]);
        $set->assets()->save($asset);

        return [
            'success' => true
        ];
    }

    public function uploadThumbnail(UploadThumbnail $request, Set $set, Uploader $uploader)
    {
        if (!$this->canMakeNewPost($set->assets()->where('asset_type_id', 1), 60))
            throw new \App\Exceptions\Custom\APIException('You can only change the thumbnail once every 60 seconds');

        $uploader->optimizer->setConstraint(768, 512);

        $asset = $uploader->upload($request->file);
        $asset->asset_type_id = AssetType::type('image')->firstOrFail()->id;
        $asset->creator_id = Auth::id();
        $set->assets()->save($asset);

        return [
            'success' => true
        ];
    }
}
