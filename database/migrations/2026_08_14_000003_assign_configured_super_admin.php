<?php

use App\Enums\Role as EnumRole;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $email = env('ADMIN_EMAIL');

        if (!$email) {
            throw new RuntimeException('ADMIN_EMAIL must be configured before assigning Super Admin.');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $superAdmin = Role::firstOrCreate([
            'name' => EnumRole::SUPER_ADMIN,
            'guard_name' => 'sanctum',
        ]);

        $superAdmin->syncPermissions(
            Permission::where('guard_name', 'sanctum')->get()
        );

        $owner = User::withoutGlobalScopes()->where('email', $email)->first();

        if (!$owner) {
            throw new RuntimeException("Cannot assign Super Admin: no user exists with email {$email}.");
        }

        User::withoutGlobalScopes()
            ->whereHas('roles', fn ($query) => $query->where('roles.id', $superAdmin->id))
            ->where('users.id', '!=', $owner->id)
            ->get()
            ->each(fn (User $user) => $user->removeRole($superAdmin));

        $owner->assignRole($superAdmin);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Keep the account and role intact when rolling back this assignment.
    }
};
