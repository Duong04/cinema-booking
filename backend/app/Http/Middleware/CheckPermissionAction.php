<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckPermissionAction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission, $action): Response
    {
        $user = auth()->user();

        if (!$user || !$user->role) {
            throw new HttpException(403, 'Forbidden');
        }

        if (
            strtoupper($user->role->name) !== 'SUPER ADMIN'
            && (!$user->hasPermission($permission) || !$user->hasAction($permission, $action, $user->role_id))
        ) {
            throw new HttpException(403, 'Forbidden');
        }

        return $next($request);
    }
}
