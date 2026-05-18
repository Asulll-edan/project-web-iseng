<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check() && $request->isMethod('post') || $request->isMethod('put') || $request->isMethod('delete')) {
            try {
                ActivityLog::create([
                    'user_id'    => Auth::id(),
                    'action'     => $request->method() . ' ' . $request->path(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Exception $e) {
                // silently fail
            }
        }

        return $response;
    }
}