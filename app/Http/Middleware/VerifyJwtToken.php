<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

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
        // $headers = apache_request_headers();

        // if (isset($headers['Authorization'])) {
        //     $authorizationHeader = $headers['Authorization'];

        //     if (strpos($authorizationHeader, 'Bearer ') === 0) {
        //         $jwt_token = substr($authorizationHeader, 7); 
        //         $user = User::where('jwt_token', '=', $jwt_token)->first();

        //         if (!empty($user) && $user->status == 'inactive') {
        //             abort(403, trans('msg.detail.inactive'));
        //         }

        //         if (empty($user)) {
        //             abort(403, trans('msg.jwt.unauthorized'));
        //         }
        //     }
        // }

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
