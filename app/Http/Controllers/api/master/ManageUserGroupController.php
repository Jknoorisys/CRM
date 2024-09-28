<?php

namespace App\Http\Controllers\api\master;

use App\Http\Controllers\Controller;
use App\Models\UserGroups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ManageUserGroupController extends Controller
{
    public function list(Request $request) {
        $validator = Validator::make($request->all(), [
            'page_no'   => ['required', 'numeric'],
            'per_page'  => ['numeric'],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {            
            $limit = $request->input(key: 'per_page', default: 10);  
            $pageNo = $request->input(key: 'page_no', default: 1); 
            $offset = ($pageNo - 1) * $limit;

            $query = UserGroups::query();

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $total = $query->count();
            $userGroups = $query->limit($limit)->offset($offset)->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status'    => 'success',
                'message'   => trans('msg.list.success'),
                'total'     => $total,
                'data'      => $userGroups,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('user_groups')],
            'login_access' => ['required', 'boolean'],
        ]);

        if ($request->login_access) {
            $validator = Validator::make($request->all(), [
                'contact_permissions' => ['nullable', 'string', 'max:255', 'required_without_all:lead_permissions,activity_permissions'],
                'lead_permissions' => ['nullable', 'string', 'max:255', 'required_without_all:contact_permissions,activity_permissions'],
                'activity_permissions' => ['nullable', 'string', 'max:255', 'required_without_all:contact_permissions,lead_permissions'],
            ]);
        }

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $insert = UserGroups::create([
                'name' => $request->name,
                'login_access' => $request->login_access,
                'contact_permissions' => $request->contact_permissions,
                'lead_permissions' => $request->lead_permissions,
                'activity_permissions' => $request->activity_permissions,
            ]);

            if ($insert) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.add.success'),
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.add.failed'),
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

    public function view(Request $request) {
        $validator = Validator::make($request->all(), [
            'group_id'   => ['required','numeric', Rule::exists('user_groups', 'id')],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $group = UserGroups::where('id', '=', $request->group_id)->first();
            if (!empty($group)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.detail.success'),
                    'data'      => $group,
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

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'group_id'   => ['required','numeric', Rule::exists('user_groups', 'id')],
            'name' => ['nullable', 'string', 'max:255', Rule::unique('user_groups')->ignore($request->group_id)],
            'login_access' => ['nullable', 'boolean'],
            'contact_permissions' => ['nullable', 'string', 'max:255'],
            'lead_permissions' => ['nullable', 'string', 'max:255'],
            'activity_permissions' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $group = UserGroups::where('id', '=', $request->group_id)->first();
            if (empty($group)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.update.not-found', ['entity' => 'user group']),
                ], 400);
            }

            $update = UserGroups::where('id', '=', $request->group_id)->update([
                'name' => $request->name,
                'login_access' => $request->login_access,
                'contact_permissions' => $request->contact_permissions,
                'lead_permissions' => $request->lead_permissions,
                'activity_permissions' => $request->activity_permissions,
            ]);

            if ($update) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.update.success'),
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.update.failed'),
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

    public function changeStatus(Request $request) {
        $validator = Validator::make($request->all(), [
            'group_id'   => ['required','numeric', Rule::exists('user_groups', 'id')],
            'status'   => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400); 
        }

        try {
            $group = UserGroups::where('id', '=', $request->group_id)->first();
            if (empty($group)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.change-status.not-found', ['entity' => 'task status']),
                ], 400);
            }

            $update = UserGroups::where('id', '=', $request->group_id)->update(['status' => $request->status]);

            if ($update) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.change-status.success'),
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.change-status.failed'),
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

    public function delete(Request $request) {
        $validator = Validator::make($request->all(), [
            'group_id'   => ['required','numeric', Rule::exists('user_groups', 'id')],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);   
        }

        try {
            $group = UserGroups::where('id', '=', $request->group_id)->first();
            if (empty($group)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.change-status.not-found', ['entity' => 'task status']),
                ], 400);
            }

            $delete = UserGroups::where('id', '=', $request->group_id)->delete();

            if ($delete) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.delete.success'),
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.delete.failed'),
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
