<?php

namespace App\Http\Middleware;

use App\Services\ResponseService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MultiRoleAuth
{
    public function handle(Request $request, Closure $next)
    {
        // ✅ Get user from sanctum
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return (new ResponseService())->error('Unauthenticated', 401);
        }

        // ✅ IMPORTANT: Bind user to request (so $request->user() works)
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}