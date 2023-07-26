<?php

Route::group(['domain' => config('site.url')], function () {

    // ONE TIME SPECIAL
    //Route::get('aeoroll', 'PageController@aeo')->middleware('throttle:60');

    Route::get('/', 'PageController@index')->name('index');
    Route::get('terms', 'PageController@tos')->name('tos');
    Route::get('privacy', 'PageController@privacy')->name('privacy');
    Route::get('staff/{page?}', 'PageController@staff')->name('staff');
    Route::get('rules', 'PageController@rules')->name('rules');

    Route::get('blog', 'PageController@blog');

    Route::get('cssguide', fn () => view('statics.cssguide'));

    Route::get('events', fn () => redirect('https://blog.brick-hill.com/event/'));

    // TODO: https://twitter.com/justsanjit/status/1514943541612527616

    // FORUM ROUTES //

    Route::namespace('Forum')->group(function () {
        Route::get('forum', 'ForumController@index')->name('forum');
        Route::get('forum/{board}/{page?}', 'ForumController@board')->whereNumber('board')->middleware('can:view,board');
        Route::get('forum/thread/{thread}/{page?}', 'ForumController@thread')->name('thread')->middleware('can:view,thread');
        Route::get('forum/post/{post}', 'ForumController@threadPageFromPost')->name('threadPageFromPost');

        Route::middleware('auth')->group(function () {
            Route::get('forum/my_posts/{page?}', 'ForumController@myPosts');
            Route::get('forum/bookmarks/{page?}', 'ForumController@myBookmarks');
            Route::get('forum/drafts/{page?}', 'ForumController@myDrafts')->name('drafts');

            Route::get('forum/{board}/create', 'CreateController@createThreadPage');
            Route::post('forum/{board}/create/{draft?}', 'CreateController@createThread')->name('createThread')->middleware('throttle:15,1');

            Route::get('forum/reply/{thread}', 'CreateController@createReplyPage')->middleware('can:view,thread');
            Route::post('forum/reply/{thread}', 'CreateController@createReply')->middleware('can:view,thread');

            Route::get('forum/reply/{thread}/quote/{post}', 'CreateController@createQuotePage')->middleware('can:view,thread');
            Route::post('forum/reply/{thread}/quote/{post}', 'CreateController@createQuote')->middleware('can:view,thread');

            Route::post('forum/{thread}/bookmark', 'CreateController@bookmark')->name('bookmark')->middleware('can:view,thread');

            Route::get('forum/draft/{draft}', 'ForumController@viewDraft')->name('draft')->middleware('can:view,draft');
            Route::post('forum/draft/{board}/create', 'CreateController@createDraft')->middleware('can:view,board')->name('createDraft');
            Route::post('forum/draft/{draft}/update', 'CreateController@updateDraft')->middleware('can:view,draft')->name('updateDraft');
            Route::post('forum/draft/{draft}/delete', 'CreateController@deleteDraft')->middleware('can:view,draft')->name('deleteDraft');
        });
    });

    // USER ROUTES //

    Route::namespace('User')->group(function () {
        Route::namespace('Authentication')->group(function () {
            Route::get('login', 'LoginController@loginPage')->middleware('guest')->name('loginPage');
            Route::post('login', 'LoginController@login')->middleware('guest')->name('login');

            Route::post('logout', 'LoginController@logout')->name('logout');
            Route::post('logoutall', 'LoginController@LogoutOtherDevices')->middleware('auth');

            Route::post('2fa/gettoken', 'TwoFactorController@getToken')->middleware('auth');
            Route::post('2fa/setuptoken', 'TwoFactorController@setupToken')->middleware('auth');
            Route::post('2fa/remove', 'TwoFactorController@remove2FA')->middleware('auth');
            Route::post('2fa/newrecoverycodes', 'TwoFactorController@newRecoveryCodes')->middleware('auth');
            Route::post('2fa/check', 'TwoFactorController@verify2FA')->middleware('auth')->name('check2FA');
            Route::get('2fa/logout', 'TwoFactorController@logout2fa')->middleware('auth');
        });

        Route::get('register', 'CreateController@registerPage')->middleware('guest');
        Route::post('register', 'CreateController@register')->middleware('throttle:3,5')->name('register');

        Route::get('password/forgot', 'EmailController@passwordPage')->middleware('guest')->name('password');
        Route::post('password/forgot', 'EmailController@sendPasswordEmail')->middleware('throttle:emails')->name('forgotPassword');
        Route::post('password/finish', 'EmailController@resetForgotPassword')->name('resetForgotPassword');

        Route::get('email/revert', 'EmailController@revertEmail')->name('emailRevert');

        Route::get('user/{id}', 'UserController@profilePage')->name('profilePage');
        Route::get('user/{id}/clans', 'UserController@userClans')->name('userClans');
        Route::get('user/{id}/friends/{page?}', 'UserController@userFriends')->name('userFriends');

        Route::middleware('auth')->group(function () {
            Route::get('settings', 'EditController@settingsPage')->name('settings');
            Route::get('settings/data', 'EditController@settingsAPI')->name('settingsApi');
            Route::post('settings', 'EditController@updateSettings')->name('changeSettings');
            Route::get('customize', 'RenderController@customizePage')->name('customize');
            Route::get('sets', 'UserController@setsPage')->name('mySets');
            Route::get('currency', 'UserController@currencyPage')->name('currency');
            Route::post('currency', 'EditController@transferCurrency')->name('transferCurrency');
            Route::get('trade/create/{id}', 'TradeController@newTrade')->name('sendTrade');
            Route::post('trade/create/{id}', 'TradeController@newTradeSend')->name('sendTradePost');
            Route::get('trades/{id?}', 'TradeController@tradesPage')->name('trades');
            Route::post('trade', 'TradeController@trade')->name('modifyTrade');

            Route::get('dashboard', 'UserController@dash')->name('dashboard');
            Route::post('status', 'CreateController@newStatus')->name('status');
            Route::post('description', 'EditController@updateDescription')->name('description');

            Route::get('friends/{page?}', 'UserController@friends');
            Route::post('friends', 'CreateController@postFriends')->name('friend')->middleware('throttle:60,1');
            Route::get('messages/{page?}', 'UserController@messages');
            Route::get('messages/sent/{page?}', 'UserController@outbox');
            Route::get('message/{id}', 'UserController@viewMessage')->name('viewMessage');
            Route::get('message/{id}/send', 'CreateController@createMessage')->name('sendMessage')->middleware('throttle:30,1');
            Route::post('messages', 'CreateController@postMessage')->name('message');

            Route::get('report/{type}/{id}', 'ReportController@report');
            Route::post('report/send', 'ReportController@sendReport')->name('sendReport');

            Route::get('banned', 'UserController@banned')->name('banned');
            Route::post('banned', 'UserController@attemptUnban')->name('postBanned');

            Route::get('email/cancelEmail', 'EmailController@cancelVerification')->name('cancelEmail');
            Route::get('email/cancelEmailAdd', 'EmailController@cancelEmailAdd')->name('cancelEmailAdd');
            Route::get('email/sendEmail', 'EmailController@sendVerification')->name('sendEmail');
            Route::get('email/verify', 'EmailController@verifyVerification')->name('verify');
        });
    });

    // MEMBERSHIP //

    Route::group(['namespace' => 'Purchases', 'middleware' => 'auth'], function () {
        Route::get('membership', 'MembershipController@index')->name('membership');
        Route::get('membership/giveaway', 'MembershipController@lottery');
    });

    // SHOP ROUTES //

    Route::group(['namespace' => 'Shop'], function () {
        Route::get('shop', 'ShopController@index');
        Route::get('shop/{item}', 'ItemController@itemPage')->where('item', '[0-9]+')->middleware('can:view,item')->name('itemPage');

        Route::middleware('auth')->group(function () {
            Route::get('shop/create', fn () => view('pages.shop.create'));

            Route::get('shop/{item}/edit', 'UploadController@editPage')->name('editPage');
            Route::post('shop/{item}/edit', 'UploadController@saveItemEdit');
            Route::post('shop/create/upload', 'UploadController@uploadItem')->name('itemUpload');

            Route::namespace('Transactions')->group(function () {
                Route::post('shop/purchase', 'ProductController@purchaseProduct');

                Route::post('shop/purchaseSpecial', 'PrivateSellerController@purchaseSpecialSeller')->name('purchaseSpecial');
                Route::post('shop/sellSpecial', 'PrivateSellerController@sellSpecial')->name('sellSpecial');
                Route::post('shop/takeSpecialOffsale', 'PrivateSellerController@takeSpecialOffsale')->name('takeSpecialOffsale');

                Route::post('shop/buyRequest', 'BuyRequestController@buyRequest')->name('buyRequest');
                Route::post('shop/cancelBuyRequest', 'BuyRequestController@cancelBuyRequest')->name('cancelBuyRequest');
            });
        });
    });

    Route::group(['namespace' => 'API', 'middleware' => 'auth'], function () {
        Route::post('comments/create', 'PolymorphicAPIController@createComment')->middleware('can:create,App\Models\Polymorphic\Comment');
        Route::post('favorites/create', 'PolymorphicAPIController@toggleFavorite')->middleware('throttle:20');
        Route::post('wishlists/create', 'PolymorphicAPIController@toggleWishlist')->middleware('throttle:20');
    });

    // GAMES ROUTES //

    Route::group(['namespace' => 'Game'], function () {
        Route::get('play', 'GameController@play')->name('play');
        Route::get('play/{id}', 'GameController@setsPage')->name('set');

        Route::group(['middleware' => 'auth'], function () {
            Route::get('play/{id}/edit', 'EditController@editSetPage')->name('editSet');
            Route::get('play/create', 'CreateController@createSet')->name('createSet');
            Route::post('play/{set}/rate', [\App\Http\Controllers\Game\RatingController::class, 'changeRating'])->middleware('throttle:20');

            Route::post('play/{set}/edit', [\App\Http\Controllers\Game\EditController::class, 'editSet'])->middleware('can:update,set');
            Route::post('play/{set}/thumbnail', [\App\Http\Controllers\Game\CreateController::class, 'uploadThumbnail'])->middleware('can:update,set');
            Route::post('play/{set}/uploadBrk', [\App\Http\Controllers\Game\CreateController::class, 'uploadBrk'])->middleware('can:updateDedicated,set');
            Route::post('play/{set}/playerAccess', [\App\Http\Controllers\Game\EditController::class, 'playerAccess'])->middleware('can:update,set');
            Route::post('play/{set}/setActive', [\App\Http\Controllers\Game\EditController::class, 'setActive'])->middleware('can:updateDedicated,set');
            Route::post('play/{set}/restartSet', [\App\Http\Controllers\Game\EditController::class, 'restartSet'])->middleware('can:updateDedicated,set');
            Route::post('play/{set}/changeType', [\App\Http\Controllers\Game\EditController::class, 'changeType'])->middleware('can:update,set');

            Route::post('play/create', 'CreateController@createSetPost')->name('createSetPost');
        });
    });

    // CLANS ROUTES //

    Route::namespace('Clan')->group(function () {
        Route::get('clan/{id}', 'ClanController@clans')->name('clan');

        Route::middleware('auth')->group(function () {
            Route::get('clans/{page?}/{search?}', 'ClanController@index')->name('clanView')->middleware('throttle:30');
            Route::get('clans/create', 'CreateController@createClan')->name('createClan');
            Route::post('clans/create', 'CreateController@createClanPost')->name('postCreateClan');
            Route::get('clan/{id}/edit', 'EditController@editClan')->name('editClan');
            Route::post('clan/edit', 'EditController@editClanPost')->name('editClanPost');
            Route::post('clan/{clan}/thumbnail', [\App\Http\Controllers\Clan\EditController::class, 'uploadThumbnail']);
            Route::post('clan/join', 'ClanController@joinClan')->name('joinClan');
            Route::post('clan/primary', 'ClanController@makePrimary')->name('makePrimary');
        });
    });

    // MISC ROUTES //

    Route::get('awards', 'PageController@awards');
    Route::get('search/{search?}', 'PageController@searchPage')->middleware('throttle:30')->name('searchUsers');
    Route::get('searchonline/{search?}', 'PageController@searchPageOnline')->middleware('throttle:30')->name('searchUsersOnline');
    Route::get('client', 'PageController@download');

    // EVENTS //
    Route::group(['namespace' => 'Events'], function () {
        Route::middleware('auth')->group(function () {
            Route::get('promocodes', [\App\Http\Controllers\Events\PromoController::class, 'displayPage']);
        });
    });

    Route::middleware('auth')->group(function () {
        Route::post('v1/events/grantItemSession', [\App\Http\Controllers\Events\EventController::class, 'grantItemSession']);

        //Route::post('events/ornaments2022', [\App\Http\Controllers\Events\EventController::class, 'ornamentsEvent'])->middleware('throttle:3,1');

        //Route::post('events/thumbnail2022', [\App\Http\Controllers\Events\EventController::class, 'thumbnailsEvent'])->middleware('throttle:10,1');
    });

    // API ROUTES //

    Route::get('api/settings/username', 'User\EditController@checkUsername');

    Route::group(['middleware' => ['auth'], 'namespace' => 'API'], function () {
        Route::get('api/user/transactions', 'User\UserAPIController@transactionAPI');

        Route::post('api/shop/render/preview', 'RenderAPIController@newPreview')->middleware('throttle:10,1'); // nobody should really rendering more than 10 a min
    });

    // FALLBACK //

    Route::fallback('PageController@fallback')->middleware('web');
});

// API SUBDOMAIN //

Route::group(['middleware' => ['api'], 'domain' => config('site.api_url')], function () {
    Route::group(['namespace' => 'API'], function () {

        // GAME AUTH APIS //

        Route::get('v1/auth/generateToken', 'SetAPIController@genToken');

        Route::middleware('auth')->group(function () {

            Route::get('v1/sets/list/recent', [\App\Http\Controllers\API\Set\ListController::class, 'listRecentlyPlayedSets']);

            Route::get('v1/sets/{set}/hostKey', [\App\Http\Controllers\API\Client\SetAPIController::class, 'getHostKey'])->middleware('can:updateHostKey,set');
            Route::post('v1/sets/{set}/newHostKey', [\App\Http\Controllers\API\Client\SetAPIController::class, 'newHostKey'])->middleware('can:updateHostKey,set')->middleware('throttle:2,1');

            Route::get('v1/sets/{set}/getAssets', [\App\Http\Controllers\API\Client\SetAPIController::class, 'getSetAssets'])->middleware('can:update,set');

            Route::get('v1/user/outfits', [\App\Http\Controllers\API\User\UserAPIController::class, 'outfits']); // TODO: deprecated
            Route::get('v2/user/outfits', [\App\Http\Controllers\API\User\UserAPIController::class, 'outfitsv2']);

            Route::post('v1/user/outfits/create', [\App\Http\Controllers\API\User\RenderAPIController::class, 'createOutfit'])->middleware('throttle:5,1');
            Route::post('v1/user/outfits/{outfit}/rename', [\App\Http\Controllers\API\User\RenderAPIController::class, 'renameOutfit'])->middleware('throttle:30,1');
            Route::post('v1/user/outfits/{outfit}/change', [\App\Http\Controllers\API\User\RenderAPIController::class, 'changeOutfit'])->middleware('throttle:5,1');
            Route::post('v1/user/outfits/{outfit}/delete', [\App\Http\Controllers\API\User\RenderAPIController::class, 'deleteOutfit'])->middleware('throttle:10,1');
            Route::post('v1/user/render/process', [\App\Http\Controllers\API\User\RenderAPIController::class, 'renderProcess'])->middleware('throttle:60,1');

            // TRADE APIS //

            Route::get('v1/user/trades/{trade}', 'User\TradeAPIController@trade')->middleware('can:view,trade');
            Route::get('v1/user/trades/{user}/{type}', 'User\TradeAPIController@myTrades')->where('type', 'inbound|outbound|history|accepted|declined')->middleware('permission_or_user:view user economy,user');

            Route::get('v1/user/economy/{user}/transactions/{type}', 'User\EconomyAPIController@transactions')->where('type', 'purchases|sales')->middleware('permission_or_user:view user economy,user');
        });
    });

    // SHOP APIS //

    // returns data related to authed user, but also contains non-personalized data
    // good candidate for moving to oauth apis?
    Route::get('v1/shop/{item}/specialData', [\App\Http\Controllers\API\Shop\SpecialAPIController::class, 'specialDatav1']);

    Route::namespace('Events')->group(function () {
        Route::post('v1/events/grantItemSession', 'EventController@grantItemSession');

        Route::post('v1/events/redeemPromo', [\App\Http\Controllers\API\Events\PromoController::class, 'redeemPromo'])->middleware('throttle:3,1');
    });

    // Membership APIs //
    Route::middleware('auth')->group(function () {
        Route::get('v1/products/all', 'Purchases\MembershipController@products');

        Route::post('v1/billing/createAsCustomer', [\App\Http\Controllers\Purchases\StripeController::class, 'createAsCustomer']);
        Route::post('v1/billing/createSession/{product}', [\App\Http\Controllers\Purchases\StripeController::class, 'createSession']);
        Route::post('v1/billing/cancelSubscription', [\App\Http\Controllers\Purchases\StripeController::class, 'cancelSubscription'])->name('billingCancelSubscription');
        Route::post('v1/billing/portal', [\App\Http\Controllers\Purchases\StripeController::class, 'billingPortal'])->name('billingPortal');
    });
});

Route::group(['domain' => config('site.url')], function () {
    Route::post('stripe/webhook', 'Purchases\StripeController@webhook');
    Route::post('amazon/webhook', 'API\WebhookController@handleBounceNotifs');
    Route::post('gitlab/webhook', 'API\WebhookController@pipelineHook');
});

// catch all to redirect from untracked domains to index
Route::fallback(function (\Illuminate\Http\Request $request) {
    return redirect()->away(config('site.url') . $request->getRequestUri());
})->middleware('web');

// LEGACY GAME APIS //
$legacy = function () {
    Route::namespace('API')->group(function () {
        Route::post('API/games/postServer', [\App\Http\Controllers\API\Client\LegacySetAPIController::class, 'postServer']);
        Route::post('API/games/invalidateToken', [\App\Http\Controllers\API\Client\LegacySetAPIController::class, 'invalidateToken']);
        Route::get('download/latest', [\App\Http\Controllers\API\Client\LegacySetAPIController::class, 'latest']);
    });
};
Route::group(['domain' => config('site.url')], $legacy);
Route::group(['domain' => 'brick-hill.com'], $legacy);
