<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EstimateApprovalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (!auth()->user()->can('approve-estimates')) {
        //     abort(403, 'You do not have permission to approve or reject estimates.');
        // }

        return $next($request);
    }
}
