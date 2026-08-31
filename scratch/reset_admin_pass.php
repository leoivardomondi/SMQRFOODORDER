<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::find(1);
if ($user) {
    $user->password = bcrypt('123456');
    $user->save();
    echo "User 1 password updated to 123456 successfully for " . $user->email . PHP_EOL;
}
