<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::all(['id', 'name', 'email', 'username']);
foreach ($users as $u) {
    echo $u->id . ' | ' . $u->name . ' | ' . $u->email . ' | ' . $u->username . PHP_EOL;
}
