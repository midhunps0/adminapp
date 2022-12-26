<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Ynotz\AccessControl\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    private $roles = [
        'Super Admin',
        'Admin',
        'Customer',
        'Seller',
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->roles as $r) {
            Role::create(['name' => $r]);
        }
    }
}
