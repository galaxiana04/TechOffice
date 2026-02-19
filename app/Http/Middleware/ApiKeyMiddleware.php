<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');
        $validApiKey = config('api.key'); // Ganti dari env()

        if (!$apiKey || $apiKey !== $validApiKey) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
