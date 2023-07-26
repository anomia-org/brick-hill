<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\API\Permissions;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{
    /**
     * Takes in a list of Permissions and returns if the current authenticated user can perform them
     * Used to properly display elements on the frontend through JS
     * Only admins can access this URL as they are the only ones who can have variable permissions ( at this time )
     * 
     * @param \App\Http\Requests\Admin\API\Permissions $request 
     * @return array 
     */
    public function canUserAccess(Permissions $request)
    {
        $allows = [];
        foreach ($request->permissions as $permission) {
            $allows[$permission] = Gate::allows($permission);
        }

        foreach ($request->model_permissions as $permission) {
            $key = $permission['permission'] . ":" . $permission['id'] . ":" . $permission['relation'];
            $class = Relation::getMorphedModel($permission['relation']);
            if (!class_exists($class)) {
                $allows[$key] = false;
                continue;
            }

            $model = resolve($class)->find($permission['id']);
            if (!$model) {
                $allows[$key] = false;
                continue;
            }

            $allows[$key] = Gate::allows($permission['permission'], $model);
        }

        return $allows;
    }
}
