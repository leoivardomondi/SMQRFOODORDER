<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $role = Role::where('name', 'Stuff')->where('guard_name', 'sanctum')->first();

        if ($role && !Role::where('name', 'Staff')->where('guard_name', 'sanctum')->exists()) {
            $role->update(['name' => 'Staff']);
        }
    }

    public function down(): void
    {
        $role = Role::where('name', 'Staff')->where('guard_name', 'sanctum')->first();

        if ($role && !Role::where('name', 'Stuff')->where('guard_name', 'sanctum')->exists()) {
            $role->update(['name' => 'Stuff']);
        }
    }
};
