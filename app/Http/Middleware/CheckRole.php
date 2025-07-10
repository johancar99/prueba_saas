<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        $user = Auth::user();
        $allowedRoles = explode('|', $roles);
        
        // Debug: agregar logging para entender qué está pasando
        \Log::info('CheckRole Middleware Debug', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_roles' => $user->getRoleNames()->toArray(),
            'allowed_roles' => $allowedRoles,
            'request_path' => $request->path(),
            'auth_guard' => Auth::getDefaultDriver()
        ]);
        
        $hasRole = false;
        foreach ($allowedRoles as $role) {
            $trimmedRole = trim($role);
            $userHasRole = $user->hasRole($trimmedRole);
            
            \Log::info("Checking role: '{$trimmedRole}', User has role: " . ($userHasRole ? 'true' : 'false'));
            
            if ($userHasRole) {
                $hasRole = true;
                break;
            }
        }
        
        \Log::info("Final result - User has allowed role: " . ($hasRole ? 'true' : 'false'));
        
        if (!$hasRole) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para acceder a este recurso'
            ], 403);
        }

        return $next($request);
    }
}
