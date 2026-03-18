<?php

namespace App\Http\Middleware;

use App\Services\ResponseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MultiRoleAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return (new ResponseService())->error('Unauthenticated',422);
            
        }

        return $next($request);
    }
}
