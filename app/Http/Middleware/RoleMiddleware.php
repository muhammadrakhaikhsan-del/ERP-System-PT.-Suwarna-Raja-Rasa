<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage in route: ->middleware('role:manager,accounting')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = session('user');

        // Not logged in
        if (!$user) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        // No role restriction = allow all logged in users
        if (empty($roles)) {
            return $next($request);
        }

        // Check role
        if (!in_array($user['role'], $roles)) {
            return back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
