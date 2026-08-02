<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        $isSuperAdmin = $user && (
            $user->branch_id == 0 ||
            $user->id == 1 ||
            $user->hasRole(Role::ADMIN) ||
            $user->hasRole(Role::SUPER_ADMIN) ||
            $user->hasRole('Admin') ||
            $user->hasRole('Super Admin')
        );

        abort_unless(
            $isSuperAdmin,
            403,
            'Only the super administrator can access settings.'
        );

        return $next($request);
    }
}
