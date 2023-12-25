<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManageActivityController extends Controller
{
    public function list(Request $request) {
        $validator = Validator::make($request->all(), [
            'page_no'   => ['required','numeric'],
            'search'    => ['nullable','string'],
            'status'    => ['nullable', 'numeric'],
            'user_id'   => ['nullable','numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try {            
            $limit = 10; 
            $pageNo = $request->input(key: 'page_no', default: 1); 
            $offset = ($pageNo - 1) * $limit;

            $query = Activity::query()->with(['user', 'activityStatus']);

            if ($request->has('search')) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            if ($request->has('status')) {
                $query->where('status', '=', $request->status);
            }

            if ($request->has('user_id')) {
                $query->where('user_id', '=', $request->user_id);
            }

            if ($request->has('from_date') && $request->has('to_date')) {
                $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
            }

            $total = $query->count();
            $activitys = $query->limit($limit)->offset($offset)->get();

            if (!empty($activitys)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'total'     => $total,
                    'data'      => $activitys,
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
            'medium'  => ['required','numeric'],
            'summary' => ['required','string'],
            'stage'   => ['required','numeric'],
            'date'    => ['required', 'string'],
            'follow_up_date'   => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try {
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $extension = $file->getClientOriginalExtension();
                $name = time().'.'.$extension;
                $file->move('assets/uploads/activity-attachments/', $name);
                $attachment = ('assets/uploads/activity-attachments/'. $name);
            } else {
                $attachment = '';
            }

            $insert = Activity::create([
                'medium' => $request->medium,
                'title' => $request->title,
                'summary' => $request->summary,
                'attachment' => $attachment,
                'stage' => $request->stage,
                'reminder_date' => $request->date,
                'follow_up_date' => $request->follow_up_date,
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
            'activity_id'   => ['required','numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try {
            $activity = Activity::where('id', '=', $request->activity_id)->with('medium')->first();
            if (!empty($activity)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.detail.success'),
                    'data'      => $activity,
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
            'activity_id'   => ['required','numeric'],
            'medium'  => ['required','numeric'],
            'summary'   => ['required','string'],
            'date'   => ['required', 'string'],
            'follow_up_date'   => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try {
            $activity = Activity::where('id', '=', $request->activity_id)->first();
            if (empty($activity)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.update.not-found', ['entity' => 'activity']),
                ], 400);
            }

            if ($request->hasFile('attachment')) {
                if (!empty($activity->attachment)) {
                    unlink($activity->attachment);
                }

                $file = $request->file('attachment');
                $extension = $file->getClientOriginalExtension();
                $name = time().'.'.$extension;
                $file->move('assets/uploads/activity-attachments/', $name);
                $attachment = ('assets/uploads/activity-attachments/'. $name);
            } else {
                $attachment = $activity->attachment;
            }

            $update = Activity::where('id', '=', $request->activity_id)->update([
                'medium' => $request->medium,
                'summary' => $request->summary,
                'attachment' => $attachment,
                'reminder_date' => $request->date,
                'follow_up_date' => $request->follow_up_date,
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

    public function changeStage(Request $request) {
        $validator = Validator::make($request->all(), [
            'activity_id' => ['required','numeric'],
            'stage'       => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try {
            $activity = Activity::where('id', '=', $request->activity_id)->first();
            if (empty($activity)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.change-status.not-found', ['entity' => 'activity']),
                ], 400);
            }

            $update = Activity::where('id', '=', $request->activity_id)->update(['status' => $request->satge]);

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
            'activity_id' => ['required','numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try {
            $activity = Activity::where('id', '=', $request->activity_id)->first();
            if (empty($activity)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.change-status.not-found', ['entity' => 'activity']),
                ], 400);
            }

            $delete = Activity::where('id', '=', $request->activity_id)->delete();

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
