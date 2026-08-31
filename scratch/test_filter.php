<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Enums\Role as EnumRole;

$users = User::with('roles')->whereDoesntHave('roles', function ($q) {
    $q->where('id', EnumRole::CUSTOMER)->orWhere('name', 'Customer');
})->orderBy('name', 'asc')->get();

echo "Non-Customer Users Count: " . $users->count() . PHP_EOL;
foreach ($users as $u) {
    $roleName = $u->roles->first()?->name ?? 'No Role';
    echo $u->id . ' | ' . $u->name . ' | ' . $u->email . ' | Role: ' . $roleName . PHP_EOL;
}
