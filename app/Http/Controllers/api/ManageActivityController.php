<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ManageActivityController extends Controller
{
    public function list(Request $request) {
        $validator = Validator::make($request->all(), [
            'page_no'   => ['required','numeric'],
            'lead_id'   => ['required','string', Rule::notIn(['undefined'])],
            'search'    => ['nullable','string'],
            'medium'    => ['nullable', 'numeric'],
            'user_id'   => ['nullable', 'numeric'],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {            
            $limit = 10; 
            $pageNo = $request->input(key: 'page_no', default: 1); 
            $offset = ($pageNo - 1) * $limit;

            $query = Activity::query()->with(['lead' ,'medium', 'user'])->where('lead_id', '=', $request->lead_id);

            if ($request->has('search')) {
                $query->where('summary', 'like', '%' . $request->search . '%');
            }

            if ($request->has('medium') && !empty($request->medium)) {
                $query->where('medium', '=', $request->medium);
            }

            if ($request->has('user_id') && !empty($request->user_id)) {
                $query->where('user_id', '=', $request->user_id);
            }

            if ($request->has('from_date') && $request->has('to_date') && !empty($request->from_date) && !empty($request->to_date)) {
                $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
            }

            $total = $query->count();
            $activities = $query->limit($limit)->offset($offset)->orderBy('created_at', 'desc')->get();

            if (!empty($activities)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'total'     => $total,
                    'data'      => $activities,
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
            'lead_id' => ['required','string', Rule::notIn(['undefined'])],
            'medium'  => ['required','numeric'],
            'stage'   => ['required','numeric'],
            'summary' => ['required','string'],
            'follow_up_date'   => ['required', 'string'],
            'user_id' => ['required', 'numeric'],
            'attachment' => ['nullable', 'mimes:jpeg,jpg,png,gif,doc,docx,pdf,txt,xlsx,xls,zip,rar', 'max:2048'],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
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
                'lead_id' => $request->lead_id,
                'user_id' => $request->user_id,
                'stage' => $request->stage,
                'medium' => $request->medium,
                'title' => $request->title,
                'summary' => $request->summary,
                'attachment' => $attachment,
                'follow_up_date' => $request->follow_up_date,
            ]);

            if ($insert) {
                Lead::where('id', '=', $request->lead_id)->update(['last_contacted_date' => Carbon::now()]);
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
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $activity = Activity::where('id', '=', $request->activity_id)->with(['lead' ,'medium', 'lead.stage', 'user'])->first();
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
            'lead_id' => ['required','string', Rule::notIn(['undefined'])],
            'user_id' => ['required', 'numeric'],
            'medium'  => ['required','numeric'],
            'summary' => ['required','string'],
            'follow_up_date'   => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
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
                'lead_id' => $request->lead_id,
                'user_id' => $request->user_id,
                'medium' => $request->medium,
                'summary' => $request->summary,
                'attachment' => $attachment,
                'follow_up_date' => $request->follow_up_date,
            ]);

            if ($update) {
                Lead::where('id', '=', $request->lead_id)->update(['last_contacted_date' => Carbon::now()]);
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
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
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

            $update = Lead::where('id', '=', $activity->lead_id)->update(['stage' => $request->stage, 'last_contacted_date' => Carbon::now()]);

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
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
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
