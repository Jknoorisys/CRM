<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function getProfile(Request $request) {
        $validator = Validator::make($request->all(), [
            'login_id'   => ['required','numeric'],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $user = User::where('id', '=', $request->login_id)->first();
            if (!empty($user) && $user->status == 'inactive') {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.detail.inactive'),
                ], 400);
            }

            if (!empty($user)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.detail.success'),
                    'data'      => $user,
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.detail.failed'),
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'login_id'   => ['required','numeric'],            
            'old_password' => 'required',
            'new_password'   => ['required', 'min:6', 'max:20'],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        } 

        try {
            
            $old_password = $request->old_password;
            $new_password = $request->new_password;

            $user = User::where('id', '=', $request->login_id)->first();
            if (!empty($user) && $user->status == 'inactive') {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.detail.inactive'),
                ], 400);
            }

            if(!empty($user)) 
            {
                if (Hash::check($old_password, $user->password)) {

                    $user->password = Hash::make($new_password);
                    $update = $user->save();

                    if ($update) {
                        return response()->json([
                            'status'    => 'success',
                            'message'   => trans('msg.change-password.success'),
                        ], 200);
                    } else {
                        return response()->json([
                            'status'    => 'failed',
                            'message'   => trans('msg.change-password.failed'),
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status'    => 'failed',
                        'message'   => trans('msg.change-password.invalid'),
                    ], 400);
                }
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.change-password.not-found'),
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function assignedTasks(Request $request) {
        $validator = Validator::make($request->all(), [
            'login_id'   => ['required','numeric'],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $user = User::where('id', '=', $request->login_id)->with('tasks')->first();
            if (!empty($user) && $user->status == 'inactive') {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.detail.inactive'),
                ], 400);
            }

            if (!empty($user)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $user,
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.list.failed'),
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
