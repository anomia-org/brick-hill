<?php

namespace Database\Seeders\Required;

use Illuminate\Database\Seeder;

use App\Models\Permission\Role;

use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionsByRole = collect([
            'moderator' => [
                1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 13, 14, 18
            ],
            'administrator' => [
                1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19
            ],
            'asset-developer' => [
                17
            ],
            'super-admin' => []
        ]);

        $roles = $permissionsByRole->keys()->map(function ($role) {
            return ['name' => $role, 'guard_name' => 'web'];
        });
        Role::insert($roles->toArray());

        $permissionIdsByRole = [
            'moderator' => $permissionsByRole['moderator'],
            'administrator' => $permissionsByRole['administrator'],
            'asset-developer' => $permissionsByRole['asset-developer']
        ];

        foreach ($permissionIdsByRole as $role => $permissionIds) {
            $role = Role::whereName($role)->first();

            DB::table('role_has_permissions')
                ->insert(
                    collect($permissionIds)->map(fn ($id) => [
                        'role_id' => $role->id,
                        'permission_id' => $id
                    ])->toArray()
                );
        }
    }
}
