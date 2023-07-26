<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Custom\APIException;
use App\Exceptions\Custom\InvalidDataException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\User\CommentResource;
use App\Http\Requests\Polymorphic\{
    CreateComment,
    ToggleFavorite,
    ToggleWishlist
};

use App\Models\Polymorphic\{
    Comment,
    Favorite,
    Wishlist
};

class PolymorphicAPIController extends Controller
{
    use \App\Traits\Controllers\Polymorphic,
        \App\Traits\Controllers\PostLimit;

    public function toggleWishlist(ToggleWishlist $request)
    {
        $class = $this->retrieveModel('wishlists', $request->polymorphic_type);
        $asset = $class->findOrFail($request->wishlistable_id);

        if (!$asset->isWishlistable())
            throw new InvalidDataException("Item is unable to be wishlisted");

        Wishlist::updateOrCreate([
            'user_id' => Auth::id(),
            'wishlistable_id' => $request->wishlistable_id,
            'wishlistable_type' => $request->polymorphic_type
        ], [
            'active' => $request->toggle
        ]);

        return response()
            ->json(['success' => true]);
    }

    public function toggleFavorite(ToggleFavorite $request)
    {
        Favorite::updateOrCreate([
            'user_id' => Auth::id(),
            'favoriteable_id' => $request->favoriteable_id,
            'favoriteable_type' => $request->polymorphic_type
        ], [
            'active' => $request->toggle
        ]);

        return response()
            ->json(['success' => true]);
    }

    public function comments(Request $request)
    {
        $class = $this->retrieveModel('comments', $request->polymorphic_type);
        $asset = $class->findOrFail($request->id);

        return CommentResource::paginateCollection($asset->comments()->with('author')->paginateByCursor(['id' => 'desc']));
    }

    public function createComment(CreateComment $request)
    {
        if (!$this->canMakeNewPost(Auth::user()->comments(), 30))
            throw new APIException('You can only make a new comment every 30 seconds');

        Comment::create([
            'author_id' => Auth::id(),
            'commentable_id' => $request->commentable_id,
            'commentable_type' => $request->polymorphic_type,
            'comment' => $request->comment,
            'scrubbed' => 0
        ]);

        return [
            'success' => true
        ];
    }
}
