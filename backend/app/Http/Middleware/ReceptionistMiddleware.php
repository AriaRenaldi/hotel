<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReceptionistMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user || ($user->role !== 'receptionist' && $user->role !== 'admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Receptionist access required.'
            ], 403);
        }

        return $next($request);
    }
}