<?php

namespace App\Http\Controllers\API\Set;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use App\Models\Set\{
    Set,
    SetGenre
};

use App\Http\Resources\Set\SetSmallResource;

class ListController extends Controller
{
    public function listSets(Request $request)
    {
        $query = Set::where(function ($q) {
            $q->whereHas('server', function ($q) {
                $q->where('last_post', '>=', Carbon::now()->subMinutes(6));
            })
                ->orWhere('is_dedicated', 1);
        })->with('creator:username,id', 'server', 'thumbnailAsset');
        // other sorts
        $query = match (true) {
            $request->sort == 'featured' => $query->where(fn($q) => $q->where('is_featured', true)->orWhere('playing', '>', 0)),
            default => $query
        };
        // genres selected
        $query = match (true) {
            // TODO: doing a whereIn on a raw input with no validation :thinking:
            $request->filled('types') && is_array($request->types) => $query->whereIn('genre_id', SetGenre::whereIn('name', $request->types)->get('id')),
            default => $query
        };
        // direction of sort
        $query = match (true) {
            // TODO: cursor paginator fails with this query for some reason, `favorites_count` is not found in where clause even though its used in the orderBy?
            // TODO: am i missing some feature about SQL here? -- also an issue where the paginator will fail when using orderByRaw as its not given in the same format
            /*$request->direction == 'asc' && $request->sort == 'Most Favorited' => $query->orderBy('favorites_count', 'ASC')->orderBy('id', 'ASC'),
            $request->direction != 'asc' && $request->sort == 'Most Favorited' => $query->orderBy('favorites_count', 'DESC')->orderBy('id', 'DESC'),*/
            $request->direction == 'asc' => $query->orderBy('playing', 'ASC')->orderBy('id', 'ASC'),
            default => $query->orderBy('playing', 'DESC')->orderBy('id', 'DESC')
        };
        // if a search should be added
        $query = match (true) {
            $request->filled('search') => $query->where('name', 'like', "%$request->search%"),
            default => $query
        };
        return SetSmallResource::paginateCollection($query->paginateByCursor());
    }

    public function listRecentlyPlayedSets(Request $request)
    {
        $query = Auth::user()->playedSets()->whereIn('id', function ($q) {
            $q->selectRaw('max(`id`)')->from('played_sets')->where('user_id', Auth::id())->groupBy('set_id');
        })->with('set.creator:username,id', 'set', 'set.server', 'set.thumbnailAsset');
        // genres selected and search
        // combine these to optimize the query to only use one subquery
        // assuming the db wouldnt have automatically did that
        $query = match (true) {
            $request->filled('types') || $request->filled('search') => $query->whereHas('set', function ($q) use ($request) {
                if ($request->filled('types')) {
                    $q->whereIn('genre_id', SetGenre::whereIn('name', $request->types)->get('id'));
                }
                if ($request->filled('search')) {
                    $q->where('name', 'like', "%$request->search%");
                }
            }),
            default => $query
        };
        // direction of sort
        $query = match (true) {
            $request->direction == 'asc' => $query->orderBy('id', 'ASC'),
            default => $query->orderBy('id', 'DESC')
        };

        return SetSmallResource::paginateCollection(
            $query
                ->paginateByCursor()
                ->mapItems(fn ($v) => $v->set)
        );
    }
}
