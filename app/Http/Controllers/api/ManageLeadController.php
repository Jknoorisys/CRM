<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManageLeadController extends Controller
{
    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'contact'       => ['required','numeric'],
            'title'         => ['required','string'],
            'description'   => ['required','string'],
            'stage'         => ['required','numeric'],
            'source'        => ['required', 'numeric'],
            'type'          => ['required', 'numeric'],
            'assigned_to'   => ['required', 'numeric'],
            'created_by'    => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try {

            $insert = Lead::create([
                'contact'       => $request->contact,
                'title'         => $request->title,
                'description'   => $request->description,
                'stage'         => $request->stage,
                'source'        => $request->source,
                'type'          => $request->type,
                'assigned_to'   => $request->assigned_to,
                'created_by'    => $request->created_by,
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

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_no'      => ['required','numeric'],
            'search'       => ['nullable','string'],
            'contact'      => ['nullable', 'numeric'],
            'stage'        => ['nullable', 'numeric'],
            'type'         => ['nullable', 'numeric'],
            'source'       => ['nullable','numeric'],
            'assigned_to'  => ['nullable','numeric'],
            'created_by'   => ['nullable','numeric'],
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }
        try
        {
            $limit = 10; 
            $pageNo = $request->input(key: 'page_no', default: 1); 
            $offset = ($pageNo - 1) * $limit;

            $query = Lead::query()->with(['contact', 'stage', 'source', 'type', 'assignedTo', 'createdBy']);

            if ($request->has('search'))
            {
                $query->where(function ($query) use ($request) 
                {
                    $query->where('id', 'like', '%' . $request->search . '%')
                          ->orWhere('title', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->has('contact')) 
            {
                $query->where('contact', '=', $request->contact);
            }

            if ($request->has('stage')) 
            {
                $query->where('stage', '=', $request->stage);
            }

            if ($request->has('source')) 
            {
                $query->where('source', '=', $request->source);
            }

            if ($request->has('type')) 
            {
                $query->where('type', '=', $request->type);
            }

            if ($request->has('assigned_to')) 
            {
                $query->where('assigned_to', '=', $request->assigned_to);
            }

            if ($request->has('created_by')) 
            {
                $query->where('created_by', '=', $request->created_by);
            }

            if ($request->has('from_date') && $request->has('to_date')) 
            {
                $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
            }

            $total = $query->count();
            $leads = $query->limit($limit)->offset($offset)->orderBy('id','DESC')->get();

            if (!empty($leads)) 
            {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'total'     => $total,
                    'data'      => $leads,
                ], 200);
            } 
            else 
            {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.list.failed'),
                ], 400);
            }
        } 
        catch (\Throwable $e) 
        {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function view(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => ['required','alpha_num'],
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try
        {
            $lead = Lead::where('id', '=', $request->lead_id)->with(['contact', 'stage', 'source', 'type', 'assignedTo', 'createdBy'])->first();
            if (!empty($lead)) 
            {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.detail.success'),
                    'data'      => $lead,
                ], 200);
            } 
            else 
            {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.detail.failed'),
                ], 400);
            } 
        }
        catch (\Throwable $e) 
        {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id'       => ['required','alpha_num'],
            'contact'       => ['nullable','numeric'],
            'title'         => ['nullable','string'],
            'description'   => ['nullable','string'],
            'stage'         => ['nullable','numeric'],
            'source'        => ['nullable', 'numeric'],
            'type'          => ['nullable', 'numeric'],
            'assigned_to'   => ['nullable', 'numeric'],
            'created_by'    => ['nullable', 'numeric'],
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try 
        {
            $lead = Lead::where('id', '=', $request->lead_id)->first();
            if (empty($lead)) 
            {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.update.not-found', ['entity' => 'Lead']),
                ], 400);
            }
            else
            {
                $update = Lead::where('id', '=', $request->lead_id)->update([
                    'contact'      => $request->contact ? $request->contact : $lead->contact,
                    'stage'        => $request->stage ? $request->stage : $lead->stage,
                    'source'       => $request->source ? $request->source : $lead->source,
                    'type'         => $request->type ? $request->type : $lead->type,
                    'assigned_to'  => $request->assigned_to ? $request->assigned_to : $lead->assigned_to,
                    'created_by'   => $request->created_by ? $request->created_by : $lead->created_by,
                    'title'        => $request->title ? $request->title : $lead->title,
                    'description'  => $request->description ? $request->description : $lead->description,
                    "updated_at"   => date('Y-m-d H:i:s')
                ]);

                if ($update) 
                {
                    return response()->json([
                        'status'    => 'success',
                        'message'   => trans('msg.update.success'),
                    ], 200);
                }
                else
                {
                    return response()->json([
                        'status'    => 'failed',
                        'message'   => trans('msg.update.failed'),
                    ], 400);
                }
            }
        }
        catch (\Throwable $e) 
        {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function changeStage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => ['required','alpha_num'],
            'stage'   => ['required', 'numeric'],
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'stage'     => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try
        {
            $lead = Lead::where('id', '=', $request->lead_id)->first();
            if (empty($lead)) 
            {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.change-stage.not-found', ['entity' => 'Lead']),
                ], 400);
            }
            else
            {
                $update = Lead::where('id', '=', $request->lead_id)->update(['stage' => $request->stage]);

                if ($update) 
                {
                    return response()->json([
                        'status'    => 'success',
                        'message'   => trans('msg.change-stage.success'),
                    ], 200);
                } 
                else 
                {
                    return response()->json([
                        'status'    => 'failed',
                        'message'   => trans('msg.change-stage.failed'),
                    ], 400);
                }
            }
        }
        catch (\Throwable $e) 
        {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => ['required','alpha_num'],
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'stage'     => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try
        {
            $lead = Lead::where('id', '=', $request->lead_id)->first();
            if (empty($lead)) 
            {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.update.not-found', ['entity' => 'Lead']),
                ], 400);
            }
            else
            {
                $delete = Lead::where('id', '=', $request->lead_id)->delete();

                if ($delete) 
                {
                    return response()->json([
                        'status'    => 'success',
                        'message'   => trans('msg.delete.success'),
                    ], 200);
                } 
                else 
                {
                    return response()->json([
                        'status'    => 'failed',
                        'message'   => trans('msg.delete.failed'),
                    ], 400);
                }
            }
        }
        catch (\Throwable $e) 
        {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    } 

}
