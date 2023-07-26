<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['domain' => config('site.url'), 'prefix' => 'api'], function () {
    Route::get('shop/item/{id}/recommended', 'Shop\ItemController@recommendedData');

    Route::get('profile/sets/{user_id}', 'User\UserController@setsAPI');

    Route::get('clans/members/{clan_id}/{rank_id}/{page?}/{clan_limit?}', 'Clan\ClanController@memberAPI');
    Route::get('clans/relations/{clan_id}/{search}/{page?}', 'Clan\ClanController@relationAPI');

    Route::get('membership/values', 'Purchases\MembershipController@membershipData');
});

Route::group(['middleware' => ['api'], 'domain' => config('site.api_url')], function () {
    Route::group(['namespace' => 'API'], function () {

        Route::post('v1/thumbnails/bulk', [\App\Http\Controllers\API\Thumbnails\ThumbnailController::class, 'bulkThumbnails']);
        Route::get('v1/thumbnails/single', [\App\Http\Controllers\API\Thumbnails\ThumbnailController::class, 'singleThumbnail']);

        // GAME APIS //

        Route::get('v1/sets/list', [\App\Http\Controllers\API\Set\ListController::class, 'listSets']);

        Route::get('v1/sets/{set}', [\App\Http\Controllers\API\Set\DataController::class, 'getSetData']);
        Route::get('v1/sets/{set}/passes', [\App\Http\Controllers\API\Set\DataController::class, 'getSetPasses']);

        Route::controller(\App\Http\Controllers\API\SetAPIController::class)->group(function () {
            Route::get('v1/auth/tokenData', 'tokenData');
            Route::get('v1/auth/verifyToken', 'verifyToken');
            Route::get('v1/games/retrieveAvatar', 'returnAvatar');
        });

        Route::controller(\App\Http\Controllers\API\Client\AvatarController::class)->group(function () {
            Route::get('v2/games/retrieveAvatar/{avatar}', 'returnAvatarv2');
        });

        Route::post('v1/games/postServer', [\App\Http\Controllers\API\Client\LegacySetAPIController::class, 'latestPostServer']);
        Route::post('v1/games/postServerCrash', [\App\Http\Controllers\API\Client\LegacySetAPIController::class, 'postServerCrash']);
        Route::get('v1/games/getBrk', [\App\Http\Controllers\API\Client\SetAPIController::class, 'getSetBrk']);

        Route::post('v1/events/ingameRedeem', [\App\Http\Controllers\Events\EventController::class, 'ingameRedeem']);

        Route::post('v1/games/registerManager', [\App\Http\Controllers\API\Client\LegacySetAPIController::class, 'registerManager']);

        Route::controller(\App\Http\Controllers\API\Shop\ItemAPIController::class)->group(function () {
            Route::get('v1/shop/list', 'latestItems');

            Route::get('v2/shop/list', 'latestItemsOpensearch');

            Route::get('v1/shop/{item}', 'itemv2');

            Route::get('v1/shop/{item}/owners', 'ownersv3');
            Route::get('v1/shop/{item}/related', 'relatedItemsv1');
            Route::get('v1/shop/{item}/series', 'seriesv1');
            Route::get('v1/shop/{item}/versions', 'versionsv1');
        });

        Route::get('v1/shop/{item}/orders', [\App\Http\Controllers\API\Shop\SpecialAPIController::class, 'ordersv1']);
        Route::get('v1/shop/{item}/chart', [\App\Http\Controllers\API\Shop\SpecialAPIController::class, 'chartv1']);
        Route::get('v1/shop/{item}/resellers', [\App\Http\Controllers\API\Shop\SpecialAPIController::class, 'resellersv2']);

        Route::get('v1/comments/{polymorphic_type}/{id}', 'PolymorphicAPIController@comments');

        Route::get('v1/assets/getPoly/{polymorphic_type}/{id}', [\App\Http\Controllers\API\Assets\AssetController::class, 'getPolyAsset']);
        Route::get('v1/assets/get/{asset}', [\App\Http\Controllers\API\Assets\AssetController::class, 'getAsset'])->middleware('cache.headers:public;max_age=31536000');

        Route::get('v2/assets/getPoly/{polymorphic_type}/{id}', [\App\Http\Controllers\API\Assets\AssetController::class, 'getPolyAssetv2']);
        Route::get('v2/assets/get/{asset}', [\App\Http\Controllers\API\Assets\AssetController::class, 'getAssetv2'])->middleware('cache.headers:public;max_age=31536000');

        Route::get('v1/client/latestVersions/release', [\App\Http\Controllers\API\Client\UpdateController::class, 'releaseVersions']);
        Route::get('v1/client/downloadInstaller/{os}', [\App\Http\Controllers\API\Client\UpdateController::class, 'downloadInstaller'])->where('os', 'windows|mac|linux');

        Route::get('v1/client/assets', [\App\Http\Controllers\API\Client\DataController::class, 'builtInAssets']);

        // EVENT APIS //

        Route::get('v1/events/activePromos', [\App\Http\Controllers\API\Events\PromoController::class, 'activePromos']);

        // USER APIS //

        Route::get('v1/user/profile', 'User\UserAPIController@profile');
        Route::get('v1/user/id', 'User\UserAPIController@nameToId');

        Route::get('v1/user/{user}/wearing', 'User\UserAPIController@wearing');
        Route::get('v1/user/{user}/crate', 'User\UserAPIController@crate');
        Route::get('v1/user/{user}/owns/{item}', 'User\UserAPIController@userOwnsItem');
        Route::get('v1/user/{user}/value', 'User\UserAPIController@tradeValue');

        // CLAN APIS //

        Route::get('v1/clan/clan', 'ClanAPIController@clan');
        Route::get('v1/clan/ranks', 'ClanAPIController@ranks');
        Route::get('v1/clan/member', 'ClanAPIController@clanMember');
    });

    // FALLBACK //

    Route::fallback('PageController@fallback');
});
