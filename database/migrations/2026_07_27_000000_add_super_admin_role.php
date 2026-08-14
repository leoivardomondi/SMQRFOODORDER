<?php

use App\Enums\Role as EnumRole;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private const SUPER_ADMIN_EMAIL = 'leoivardomondi@seamlessqrcode.com';

    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $superAdmin = Role::firstOrCreate([
            'name'       => EnumRole::SUPER_ADMIN,
            'guard_name' => 'sanctum',
        ]);
        $superAdmin->syncPermissions(Permission::where('guard_name', 'sanctum')->get());

        $admin = Role::find(EnumRole::ADMIN);
        if ($admin) {
            $admin->revokePermissionTo(
                Permission::where('name', 'settings')
                    ->where('guard_name', 'sanctum')
                    ->get()
            );
        }

        $owner = User::withoutGlobalScopes()
            ->where('email', self::SUPER_ADMIN_EMAIL)
            ->first();

        if (!$owner) {
            throw new RuntimeException(
                'Cannot assign Super Admin: no user exists with email ' . self::SUPER_ADMIN_EMAIL
            );
        }

        User::withoutGlobalScopes()
            ->whereHas('roles', fn ($query) => $query->where('roles.id', $superAdmin->id))
            ->where('users.id', '!=', $owner->id)
            ->get()
            ->each(fn (User $user) => $user->removeRole($superAdmin));

        $owner->syncRoles([$superAdmin]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $superAdmin = Role::where([
            'name'       => EnumRole::SUPER_ADMIN,
            'guard_name' => 'sanctum',
        ])->first();

        if ($superAdmin) {
            User::withoutGlobalScopes()
                ->whereHas('roles', fn ($query) => $query->where('roles.id', $superAdmin->id))
                ->get()
                ->each(function (User $user): void {
                $user->removeRole(EnumRole::SUPER_ADMIN);
                $user->assignRole(EnumRole::ADMIN);
            });
            $superAdmin->delete();
        }

        $admin = Role::find(EnumRole::ADMIN);
        $settings = Permission::where([
            'name'       => 'settings',
            'guard_name' => 'sanctum',
        ])->first();
        if ($admin && $settings) {
            $admin->givePermissionTo($settings);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
