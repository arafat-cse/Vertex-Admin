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
     * Checks whether the authenticated user holds the given Spatie permission.
     * Returns a 403 JSON response if the permission is missing.
     *
     * Usage in routes:
     *   ->middleware('permission:users.view')
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();

        if (!$user || !$user->can($permission)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. You do not have permission to perform this action.',
                'data'    => null,
                'errors'  => null,
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
