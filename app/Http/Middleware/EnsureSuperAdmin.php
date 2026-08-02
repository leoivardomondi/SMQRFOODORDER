<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(
            $request->user() && $request->user()->hasRole(Role::SUPER_ADMIN),
            403,
            'Only the super administrator can access settings.'
        );

        return $next($request);
    }
}
