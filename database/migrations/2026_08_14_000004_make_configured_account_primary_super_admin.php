<?php

use App\Enums\Role as EnumRole;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $email = env('ADMIN_EMAIL');
        $superAdmin = Role::where('name', EnumRole::SUPER_ADMIN)
            ->where('guard_name', 'sanctum')
            ->first();
        $owner = User::withoutGlobalScopes()->where('email', $email)->first();

        if (!$superAdmin || !$owner) {
            throw new RuntimeException('Configured Super Admin account or role was not found.');
        }

        $owner->syncRoles([$superAdmin]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Keep the configured account as Super Admin when rolling back.
    }
};
