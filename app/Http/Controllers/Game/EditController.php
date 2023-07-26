<?php

namespace App\Http\Controllers\Game;

use Illuminate\Support\Facades\{
    App,
    Auth
};
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

use App\Http\Requests\Game\Edit\{
    EditSet,
    ChangeType,
    PlayerAccess,
    SetActive,
};
use App\Models\Set\{
    Set,
    SetGenre
};

use Carbon\Carbon;
use AsyncAws\Sqs\SqsClient;

class EditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function editSetPage($id)
    {
        $set = Set::find($id);

        if (!$set || $set->creator_id != Auth::id())
            return redirect()->route('play');

        return view('pages.sets.edit')->with([
            'set' => $set
        ]);
    }

    public function setActive(SetActive $request, Set $set)
    {
        $asset = $set->bundledBrkAssets()->findOrFail($request->id);

        // if they already have it selected we can just ignore the request
        if ($asset->is_selected_version == 1) {
            return ['success' => true];
        }

        $asset->is_selected_version = 1;

        $set->bundledBrkAssets()->update([
            'is_selected_version' => 0
        ]);
        $asset->save();

        // TODO: hype for PHP enums, need to change these random numbers to those when come out
        $this->sendSQS($set, 3);

        return ['success' => true];
    }

    public function changeType(ChangeType $request, Set $set)
    {
        if ($request->server_type == 'dedicated') {
            $this->authorize('updateDedicated', $set);
        }

        // only do something if it will actually change the value
        if (($request->server_type == 'dedicated') != $set->is_dedicated) {
            $set->is_dedicated = ($request->server_type == 'dedicated');
            $set->host_key = Str::random(64);

            if (!$set->is_dedicated) {
                $set->playing = 0;
                $set->server()->update([
                    'last_post' => Carbon::now()->subMinutes(10)
                ]);

                $this->sendSQS($set, 1);
            }

            $set->save();
        }

        return ['success' => true];
    }

    public function restartSet(Set $set)
    {
        $this->sendSQS($set, 1);

        return ['success' => true];
    }

    public function playerAccess(PlayerAccess $request, Set $set)
    {
        $set->max_players = $request->max_players;
        $set->friends_only = (int)($request->who_can_join == 'friends');
        $set->save();

        $this->sendSQS($set, 2, [
            'max_players' => $set->max_players
        ]);

        return ['success' => true];
    }

    public function editSet(EditSet $request, Set $set)
    {
        $set->name = $request->title;
        $set->description = $request->description;

        if ($request->filled('genre')) {
            $set->genre_id = SetGenre::where('name', $request->genre)->firstOrFail()->id;
        } else {
            $set->genre_id = NULL;
        }

        $set->save();

        return ['success' => true];
    }

    private function sendSQS(Set $set, int $type, array $body = null)
    {
        if (App::environment(['local', 'testing']))
            return;
        $client = new SqsClient([
            'region'  => 'us-east-1'
        ]);

        $client->sendMessage([
            'QueueUrl'    => config('site.sqs.manager_queue'),
            'MessageBody' => json_encode([
                'type' => $type,
                'body' => !is_null($body) ? json_encode($body) : ""
            ]),
            'MessageAttributes' => [
                'SetId' => [
                    'StringValue' => (string) $set->id,
                    'DataType' => 'Number'
                ]
            ]
        ]);
    }
}
