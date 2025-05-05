<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class RoleOrPermissionMiddleware
{
    public function handle(Request $request, Closure $next, $roleOrPermission, $guard = null)
    {
        $authGuard = app('auth')->guard($guard);
        
        if ($authGuard->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $rolesOrPermissions = is_array($roleOrPermission)
            ? $roleOrPermission
            : explode('|', $roleOrPermission);

        if (! $authGuard->user()->hasAnyRole($rolesOrPermissions) && 
            ! $authGuard->user()->hasAnyPermission($rolesOrPermissions)) {
            throw UnauthorizedException::forRolesOrPermissions($rolesOrPermissions);
        }

        return $next($request);
    }
} 