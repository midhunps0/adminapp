<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ynotz\AccessControl\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@demo.com',
            'password' => Hash::make('abcd1234')
        ]);
        \App\Models\User::factory(49)->create()->each(function ($user) {
            $user->assignRole(Role::all()->random()->id);
        });
    }
}
