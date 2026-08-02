<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = Spatie\Permission\Models\Role::all();
foreach ($roles as $role) {
    echo "ID: {$role->id} | Name: {$role->name} | Guard: {$role->guard_name}\n";
}
