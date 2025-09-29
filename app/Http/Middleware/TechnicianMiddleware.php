<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TechnicianMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('filament.technician.auth.login');
        }

        if (auth()->user()->role !== 'technician') {
            abort(403, 'Access denied. Technician role required.');
        }

        if (auth()->user()->status !== 'active') {
            abort(403, 'Account is not active.');
        }

        return $next($request);
    }
}
