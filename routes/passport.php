<?php

/**
 * Collection of all routes that use OAuth
 */

Route::group(['middleware' => ['api'], 'domain' => config('site.api_url')], function () {
    Route::group(['namespace' => 'API'], function () {

        Route::get('v1/client/latestVersions/debug', [\App\Http\Controllers\API\Client\UpdateController::class, 'debugVersions'])->middleware('scope:access-workshop');
        Route::get('v1/client/downloadDebug/{os}', [\App\Http\Controllers\API\Client\UpdateController::class, 'downloadDebug'])->middleware('scope:access-workshop')->where('os', 'windows|mac|linux');

        Route::get('v1/auth/currentUser', 'User\AuthAPIController@userInformation');
    });
});
