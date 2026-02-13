<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check()) {
            Log::info('User Activity', [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->full_name,
                'action' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'timestamp' => now(),
            ]);
        }

        return $response;
    }
}