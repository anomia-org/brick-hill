<?php

namespace App\Http\Controllers\Admin\Super\API;

use App\Http\Controllers\Controller;

use App\Http\Resources\Admin\{
    RoleResource,
    PermissionResource,
    UserPermissionsResource
};

use App\Http\Requests\Admin\Super\{
    SaveRoles,
    SaveUsers,
    SaveUser
};

use App\Models\Permission\{
    Permission,
    Role
};
use App\Models\User\User;

class PermissionController extends Controller
{
    /**
     * Returns all roles stored
     * 
     * @return mixed 
     */
    public function getAllRoles()
    {
        return RoleResource::collection(Role::with('permissions')->get());
    }

    /**
     * Returns all permissions stored
     * 
     * @return mixed 
     */
    public function getAllPermissions()
    {
        return PermissionResource::collection(Permission::all());
    }

    /**
     * Returns all permissions for all users with permissions
     * 
     * @return mixed 
     */
    public function getPermissionsForUsers()
    {
        // paginate by id
        // will lead to loading wrongly if a cursor is used after a user has been added before it is used
        // there is no column in the user table to paginate based on new roles
        $paginator = User::where('power', '>', 0)
            ->with('roles:id')
            ->paginateByCursor(['power' => 'desc']);
        return UserPermissionsResource::paginateCollection($paginator);
    }

    /**
     * Saves roles to users
     * 
     * @param SaveRoles $request 
     * @return void 
     */
    public function saveRoles(SaveRoles $request)
    {
        foreach ($request->roles as $requestRole) {
            if ($requestRole['name'] == 'super-admin')
                continue;
            if (isset($requestRole['new'])) {
                $role = Role::create([
                    'name' => $requestRole['name']
                ]);
            } else {
                $role = Role::findOrFail($requestRole['id']);
            }
            $role->name = $requestRole['name'];
            $role->syncPermissions(collect($requestRole['permissions'])->pluck('id'));
        }
        return;
    }

    /**
     * Saves power level to users
     * 
     * @param SaveUsers $request 
     * @return void 
     */
    public function saveUsers(SaveUsers $request)
    {
        foreach ($request->users as $requestUser) {
            $user = User::findOrFail($requestUser['id']);
            if ($user->power >= auth()->user()->power || $requestUser['power'] >= auth()->user()->power)
                continue;
            $user->syncRoles($requestUser['roles']);
            $user->power = (count($requestUser['roles']) == 0) ? 0 : $requestUser['power'];
            $user->save();
        }
        return;
    }

    /**
     * Saves power level to individual user
     * 
     * @param SaveUser $request 
     * @return void 
     */
    public function saveUser(SaveUser $request)
    {
        $user = User::findOrFail($request->user_id);
        if ($user->power >= auth()->user()->power || $request->user_power >= auth()->user()->power)
            return;
        $user->syncRoles($request->user_roles);
        $user->power = (count($request->user_roles) == 0) ? 0 : $request->user_power;
        $user->save();
        return;
    }
}
