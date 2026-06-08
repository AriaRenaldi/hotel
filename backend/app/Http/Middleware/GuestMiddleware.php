<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user || $user->role !== 'guest') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Guest access required.'
            ], 403);
        }

        return $next($request);
    }
}