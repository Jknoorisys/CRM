<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Tasks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ManageTaskController extends Controller
{
    public function list(Request $request) {
        $validator = Validator::make($request->all(), [
            'page_no'   => ['required','numeric'],
            'per_page'  => ['numeric'],
            'search'    => ['nullable','string'],
            'status'    => ['nullable', 'numeric', Rule::exists('task_status', 'id')],
            'user_id'   => ['nullable','numeric', Rule::exists('users', 'id')],
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

            $query = Tasks::query()->with(['user', 'taskStatus']);

            if ($request->has('search') && !empty($request->search)) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', '=', $request->status);
            }

            if ($request->has('user_id') && !empty($request->user_id)) {
                $query->where('user_id', '=', $request->user_id);
            }

            if ($request->has('from_date') && $request->has('to_date') && !empty($request->from_date) && !empty($request->to_date)) {
                $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
            }

            $total = $query->count();
            $tasks = $query->limit($limit)->offset($offset)->orderBy('created_at', 'desc')->get();

            if (!empty($tasks)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'total'     => $total,
                    'data'      => $tasks,
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

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'title'  => ['required','string','max:255'],
            'description'   => ['required','string'],
            'user_id'   => ['required','numeric', Rule::exists('users', 'id')],
            'status'   => ['required', 'numeric', Rule::exists('task_status', 'id')],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $insert = Tasks::create([
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => $request->user_id,
                'status' => $request->status,
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
            'task_id'   => ['required','numeric', Rule::exists('tasks', 'id')],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $task = Tasks::where('id', '=', $request->task_id)->with(['user', 'taskStatus'])->first();
            if (!empty($task)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.detail.success'),
                    'data'      => $task,
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
            'task_id'   => ['required','numeric', Rule::exists('tasks', 'id')],
            'title'  => ['required','string','max:255'],
            'description'   => ['required','string'],
            'user_id'   => ['required','numeric', Rule::exists('users', 'id')],
            // 'status'   => ['nullable', 'numeric', Rule::exists('task_status', 'id')],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $task = Tasks::where('id', '=', $request->task_id)->first();
            if (empty($task)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.update.not-found', ['entity' => 'task']),
                ], 400);
            }

            $update = Tasks::where('id', '=', $request->task_id)->update([
                'title' => $request->title ? $request->title : $task->title,
                'description' => $request->description ? $request->description : $task->description,
                'user_id' => $request->user_id ? $request->user_id : $task->user_id,
                // 'status' => $request->status ? $request->status : $task->status,
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
            'task_id' => ['required','numeric', Rule::exists('tasks', 'id')],
            'status'   => ['required', 'numeric', Rule::exists('task_status', 'id')],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $task = Tasks::where('id', '=', $request->task_id)->first();
            if (empty($task)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.change-status.not-found', ['entity' => 'task']),
                ], 400);
            }

            $update = Tasks::where('id', '=', $request->task_id)->update(['status' => $request->status]);

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
            'task_id' => ['required','numeric', Rule::exists('tasks', 'id')],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $task = Tasks::where('id', '=', $request->task_id)->first();
            if (empty($task)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.change-status.not-found', ['entity' => 'task']),
                ], 400);
            }

            $delete = Tasks::where('id', '=', $request->task_id)->delete();

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
