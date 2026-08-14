<?php

use App\Enums\Role as EnumRole;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $superAdmin = Role::firstOrCreate([
            'name' => EnumRole::SUPER_ADMIN,
            'guard_name' => 'sanctum',
        ]);

        $superAdmin->syncPermissions(
            Permission::where('guard_name', 'sanctum')->get()
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // The role may be required by the existing super-admin assignment;
        // leave it in place when rolling back this visibility migration.
    }
};
