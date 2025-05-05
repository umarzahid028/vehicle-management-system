<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // During development, allow access to vendor routes
        if (config('app.env') === 'local') {
            return $next($request);
        }

        $user = auth()->user();
        
        // Check if user is a vendor
        if (!$user->role || !($user->role instanceof Role) || !$user->role->isVendor()) {
            abort(403, 'Unauthorized. Vendor access only.');
        }

        // Check if vendor has system access based on their vendor type
        if (!$user->hasSystemAccess()) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your vendor account does not have system access. Please contact the administrator.');
        }

        return $next($request);
    }
} 