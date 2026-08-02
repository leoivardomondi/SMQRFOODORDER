<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use App\Models\User;

$superAdminRole = Role::where('name', 'Super Admin')->first();

if ($superAdminRole) {
    echo "Cleaning up Super Admin role assignments...\n";
    $users = $superAdminRole->users;
    foreach ($users as $user) {
        // Keep Super Admin ONLY for ID 1 (Leoivard Ongule / Master Admin)
        if ($user->id !== 1 && $user->username !== 'admin') {
            $user->removeRole($superAdminRole);
            echo "Removed Super Admin role from: ID {$user->id} - {$user->name} ({$user->email})\n";
        }
    }
    
    // Ensure User ID 1 has Super Admin role
    $masterAdmin = User::find(1);
    if ($masterAdmin && !$masterAdmin->hasRole($superAdminRole)) {
        $masterAdmin->assignRole($superAdminRole);
        echo "Ensured Super Admin role for Master Admin: {$masterAdmin->name}\n";
    }

    $count = $superAdminRole->users()->count();
    echo "Updated Super Admin Member Count: {$count} Member(s)\n";
}
