<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyJwtToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     *
     * @throws \Tymon\JWTAuth\Exceptions\TokenExpiredException
     * @throws \Tymon\JWTAuth\Exceptions\TokenInvalidException
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            JWTAuth::parseToken($request);
            if (!empty($request->login_id) && $request->login_id != auth()->id()) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.jwt.unauthorized'),
                ], 403);
            }
        } catch (Exception $e) {
            if ($e instanceof TokenExpiredException) {
                return response()->json(['error' => trans('msg.jwt.expired')], 401);
            } else if ($e instanceof TokenInvalidException) {
                return response()->json(['error' => trans('msg.jwt.invalid')], 401);
            } else {
                return response()->json(['error' => trans('msg.jwt.missing')], 401);
            }
        }

        return $next($request);
    }
}
