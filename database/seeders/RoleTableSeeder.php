<?php

namespace Database\Seeders;

use App\Enums\Role as EnumRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::insert([
            [
                'name'       => 'Admin',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Customer',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Delivery Boy',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name'       => 'Waiter',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Chef',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Branch Manager',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'POS Operator',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Staff',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Role::firstOrCreate([
            'name'       => EnumRole::SUPER_ADMIN,
            'guard_name' => 'sanctum',
        ]);
    }
}
