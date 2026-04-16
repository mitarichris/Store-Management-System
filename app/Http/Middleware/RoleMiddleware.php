<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated. Contact your administrator.']);
        }

        if (!empty($roles) && !$user->hasRole($roles)) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
