<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

echo "Creating or finding 'Super Admin' role...\n";
$superAdminRole = Role::firstOrCreate([
    'name'       => 'Super Admin',
    'guard_name' => 'sanctum',
]);

echo "Syncing all permissions to Super Admin role...\n";
$allPermissions = Permission::all();
$superAdminRole->syncPermissions($allPermissions);

echo "Assigning 'Super Admin' role to users with branch_id = 0 or ID = 1...\n";
$superUsers = User::where('branch_id', 0)->orWhere('id', 1)->get();
foreach ($superUsers as $user) {
    if (!$user->hasRole('Super Admin')) {
        $user->assignRole($superAdminRole);
        echo "Assigned Super Admin role to: {$user->name} ({$user->email})\n";
    } else {
        echo "Already has Super Admin role: {$user->name} ({$user->email})\n";
    }
}

echo "Done! Super Admin role and permissions setup successfully.\n";
