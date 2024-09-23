<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                if (!User::where('email', $request->email)->exists()) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => trans('msg.login.not-found'),
                    ], 401);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => trans('msg.login.invalid'),
                    ], 401);
                }
            }

            $user = User::find(auth()->id()); 
            if ($user->status != 'active') {
                return response()->json([
                    'status' => 'failed',
                    'message' => trans('msg.login.inactive'),
                ], 401);
            }

            $token = JWTAuth::fromUser($user);

            $user->jwt_token = $token;
            $user->save();

            $user->permissions = explode(',', $user->permissions);

            $user->user_group = $user->userGroup()->first();
            return response()->json([
                'status'  => 'success',
                'message' => trans('msg.login.success'),
                'data'    => $user,
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => trans('msg.error'),
                'error' => 'Could not create token',
            ], 500);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'failed',
                'message' => trans('msg.error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
