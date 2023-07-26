<?php

Route::group(['domain' => config('site.url')], function () {
    Route::get('admin', 'AdminController@index')->name('admin');
    Route::post('admin/report', 'AdminController@seenReport')->middleware('permission:view reports');
    Route::post('admin/grant', 'AdminController@grantItem')->middleware('permission:grant items');
    Route::post('admin/currency', 'AdminController@changeCurrency')->middleware('permission:grant currency');
    Route::post('admin/transfer', 'AdminController@transferCrate')->middleware('permission:transfer crate');
    Route::post('admin/grantWorkshop', 'AdminController@grantWorkshop')->middleware('permission:grant client');
    Route::post('admin/points', 'AdminController@exchangePoints');

    Route::group(['middleware' => 'permission:manage shop'], function () {
        Route::get('admin/shop', fn () => view('pages.admin.shop'));
    });

    Route::group(['middleware' => 'api', 'prefix' => 'v1/admin/'], function () {
        Route::get('approval/items', [\App\Http\Controllers\Admin\AdminAPIController::class, 'pendingItems'])->middleware('permission:accept items');
        Route::get('approval/assets', [\App\Http\Controllers\Admin\AdminAPIController::class, 'pendingAssets'])->middleware('permission:accept items');
        Route::post('approve/item/{item}/{asset?}', [\App\Http\Controllers\Admin\AdminController::class, 'pendItems'])->middleware('permission:accept items');
        Route::post('approve/asset/{asset}', [\App\Http\Controllers\Admin\AdminController::class, 'pendAsset'])->middleware('permission:accept items');

        Route::post('thumbnails/bulk', [\App\Http\Controllers\Admin\API\ThumbnailController::class, 'bulkThumbnails'])->middleware('permission:accept items');

        Route::post('grant/membership/{user}', [\App\Http\Controllers\Admin\API\GrantController::class, 'grantMembership'])->middleware('permission:grant membership');

        Route::group(['middleware' => 'permission:manage shop'], function () {
            Route::get('shop/scheduled/{type}', [\App\Http\Controllers\Admin\API\Shop\ScheduleController::class, 'scheduledItems'])->where('type', 'pending|upcoming|past');
            Route::get('shop/unscheduled', [\App\Http\Controllers\Admin\API\Shop\ScheduleController::class, 'unscheduledItems']);

            Route::post('shop/uploadItem', [\App\Http\Controllers\Admin\API\Shop\ItemController::class, 'uploadItem']);
            Route::post('shop/scheduleItem', [\App\Http\Controllers\Admin\API\Shop\ScheduleController::class, 'scheduleItem']);

            Route::get('shop/events', [\App\Http\Controllers\Admin\API\Shop\ItemController::class, 'listEvents']);
            Route::post('shop/createEvent', [\App\Http\Controllers\Admin\API\Shop\ItemController::class, 'createEvent']);
            Route::post('shop/modifyEvent/{event}', [\App\Http\Controllers\Admin\API\Shop\ItemController::class, 'modifyEvent']);
        });

        Route::group(['middleware' => 'permission:approve item schedule'], function () {
            Route::get('shop/getRecentAssets', [\App\Http\Controllers\Admin\API\Shop\AssetController::class, 'getRecentAssets']);

            Route::post('shop/schedule/{schedule}/toggle', [\App\Http\Controllers\Admin\API\Shop\ScheduleController::class, 'approveSchedule']);
            Route::post('shop/uploadAsset', [\App\Http\Controllers\Admin\API\Shop\AssetController::class, 'uploadAsset']);
            Route::post('shop/item/{item}/updateAsset', [\App\Http\Controllers\Admin\API\Shop\AssetController::class, 'updateAsset']);
        });

        Route::controller(\App\Http\Controllers\Admin\API\SetController::class)->group(function () {
            Route::group(['middleware' => ['permission:scrub sets']], function () {
                Route::post('sets/{set}/toggleFeatured', 'toggleFeaturedStatus');

                Route::post('sets/{set}/scrubName', 'scrubName');
                Route::post('sets/{set}/scrubDesc', 'scrubDesc');
                //Route::post('sets/{set}/scrubSetImage', 'scrubImage');
            });
        });

        Route::controller(\App\Http\Controllers\Admin\API\SupportController::class)->group(function () {
            Route::group(['middleware' => 'permission:modify emails'], function () {
                Route::post('{user}/replaceEmail', 'replaceEmail')->middleware('can:update,user');
                Route::post('{user}/checkEmail', 'checkEmail')->middleware('can:view,user')->middleware('throttle:5,1');
            });

            Route::group(['middleware' => 'permission:recover tfa'], function () {
                Route::post('{user}/recoverTFA', 'sendTFARecoveryEmail')->middleware('can:update,user')->middleware('throttle:1,1');
            });
        });

        Route::controller(\App\Http\Controllers\Admin\API\LogsController::class)->group(function () {
            Route::get('logs', 'listLogs');
        });
    });

    Route::group(['middleware' => 'permission:manage forum'], function () {
        Route::post('forum/post/{post}/scrub', 'AdminForumController@scrubPost');

        Route::post('forum/thread/{thread}/scrub', 'AdminForumController@scrubThread');
        Route::post('forum/thread/{thread}/lock', 'AdminForumController@lockThread');
        Route::post('forum/thread/{thread}/delete', 'AdminForumController@deleteThread');
        Route::post('forum/thread/{thread}/pin', 'AdminForumController@pinThread');
        Route::post('forum/thread/{thread}/move', 'AdminForumController@moveThread');
        Route::post('forum/thread/{thread}/hide', 'AdminForumController@hideThread');
    });

    Route::get('admin/gift_to_all', 'AdminController@giftToAll');
    Route::post('admin/gift_to_all', 'AdminController@giftToAllPost');

    Route::group(['middleware' => 'can:view,user'], function () {
        Route::get('user/{user}/audit', 'AdminUserController@userAudit')->middleware('permission:view user economy|view linked accounts|view emails|view purchases');
        Route::get('bans/{user}/{ban}', 'AdminUserController@viewBan');
    });

    Route::group(['middleware' => 'can:update,user'], function () {
        Route::group(['middleware' => 'permission:ban'], function () {
            Route::get('user/{user}/ban/{type?}/{content?}', 'AdminUserController@banUser')->name('banUser');
            Route::get('user/{user}/unban', 'AdminUserController@unbanUser');
            Route::post('user/{user}/ban', 'AdminUserController@postBan')->name('postBan');
        });
        Route::get('user/{user}/superban', 'AdminUserController@superban')->middleware('permission:superban');

        Route::group(['middleware' => ['permission:scrub users']], function () {
            Route::post('user/{user}/scrubStatus', 'AdminUserController@scrubStatus');
            Route::post('user/{user}/scrubDesc', 'AdminUserController@scrubDesc');
            Route::post('user/{user}/scrubName', 'AdminUserController@scrubUsername');
            Route::post('user/{user}/renderAvatar', 'AdminUserController@renderAvatar');
        });
    });
    Route::post('message/{message}/scrub', 'AdminUserController@scrubMessage')->middleware('permission:scrub users');

    Route::post('shop/{item}/scrubName', 'AdminShopController@scrubItemName')->middleware('permission:scrub items');
    Route::post('shop/{item}/scrubDesc', 'AdminShopController@scrubItemDesc')->middleware('permission:scrub items');
    Route::post('shop/{item}/render', 'AdminShopController@renderItem')->middleware('permission:scrub items');

    Route::group(['middleware' => ['permission:scrub clans']], function () {
        Route::post('clan/{clan}/scrubName', 'AdminClanController@scrubClanName');
        Route::post('clan/{clan}/scrubDesc', 'AdminClanController@scrubClanDesc');
        Route::post('clan/{clan}/scrubImage', 'AdminClanController@scrubClanImage');
        Route::post('clan/{clan}/scrubTag', 'AdminClanController@scrubClanTag');
    });

    // ADMIN API ROUTES //

    Route::get('api/admin/approval', 'AdminAPIController@approvalItems')->middleware('permission:accept items|accept clans');
    Route::get('api/admin/reports', 'AdminAPIController@reports')->middleware('permission:view reports');
    Route::get('api/admin/leaderboard', 'AdminAPIController@pointsLeaderboard');

    Route::group(['namespace' => 'API'], function () {
        Route::post('admin/permissions', 'PermissionController@canUserAccess');
    });
});

// API SUBDOMAIN //

Route::group(['middleware' => ['api'], 'domain' => config('site.api_url')], function () {
    Route::group(['namespace' => 'API'], function () {
        Route::post('v1/comments/{comment}/scrub', 'PolymorphicController@scrubComment')->middleware('permission:manage comments');
    });
});
