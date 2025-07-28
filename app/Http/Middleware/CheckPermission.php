<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $user = auth()->user();
        
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account is inactive.');
        }

        if (!$user->role) {
            return redirect()->route('dashboard')->with('error', 'No role assigned to your account.');
        }

        $userPermissions = $user->role->permissions ?? [];
        
        if (!in_array($permission, $userPermissions)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
