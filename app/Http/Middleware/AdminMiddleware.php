<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user=Auth::user();
        if(!$user || $user->role != 'admin')
            {
                return response()->json([
                    'status' => false,
                    'message' => 'unauthorized. Only Admin can access this resources'
                ],403);
            }
        return $next($request);
    }
}
