<?php

Route::get('', 'PanelController@home');

Route::group(['middleware' => 'api', 'prefix' => 'v1/admin/'], function () {
    Route::post('banner', [\App\Http\Controllers\Admin\Super\API\SiteController::class, 'siteBanner']);
    Route::post('maintenance', [\App\Http\Controllers\Admin\Super\API\SiteController::class, 'maintenanceMode']);
    Route::post('workshopRelease', [\App\Http\Controllers\Admin\Super\API\SiteController::class, 'workshopRelease']);

    Route::post('promocodes/singleCode', [\App\Http\Controllers\Admin\Super\API\PromoController::class, 'newPromo']);
    Route::post('promocodes/massImport', [\App\Http\Controllers\Admin\Super\API\PromoController::class, 'massImport']);
    Route::get('promocodes/lookup/{code}', [\App\Http\Controllers\Admin\Super\API\PromoController::class, 'lookupCode']);

    Route::get('permissions/users', [\App\Http\Controllers\Admin\Super\API\PermissionController::class, 'getPermissionsForUsers']);
    Route::get('permissions/all', [\App\Http\Controllers\Admin\Super\API\PermissionController::class, 'getAllPermissions']);
    Route::get('permissions/roles', [\App\Http\Controllers\Admin\Super\API\PermissionController::class, 'getAllRoles']);
    Route::post('permissions/save/roles', [\App\Http\Controllers\Admin\Super\API\PermissionController::class, 'saveRoles']);
    Route::post('permissions/save/users', [\App\Http\Controllers\Admin\Super\API\PermissionController::class, 'saveUsers']);
    Route::post('permissions/save/user', [\App\Http\Controllers\Admin\Super\API\PermissionController::class, 'saveUser']);
});

Route::fallback(function () {
    return redirect(config('site.url') . request()->getRequestUri());
});
