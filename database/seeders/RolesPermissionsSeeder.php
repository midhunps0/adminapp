<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ynotz\AccessControl\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesPermissionsSeeder extends Seeder
{
    private $rolesPermissions = [
        'admin' => [
            'user.view_any',
            'user.create_any',
            'user.edit_any',
            'user.delete_any',
            'role.view_any',
            'role.create_any',
            'role.edit_any',
            'role.delete_any',
            'permission.view_any',
            'permission.create_any',
            'permission.edit_any',
            'permission.delete_any',
        ],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->rolesPermissions as $role => $permissions) {
            foreach ($permissions as $permission) {
                $roleObject = Role::where('name', $role)->get()->first();
                $roleObject->assignPermissions([$permission]);
            }
        }
    }
}
