<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ManageLeadController extends Controller
{
    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'contact'       => ['required','numeric', Rule::exists('contacts', 'id')],
            'title'         => ['required','string'],
            'description'   => ['required','string'],
            'stage'         => ['required','numeric', Rule::exists('stages', 'id')],
            'source'        => ['required', 'numeric', Rule::exists('sources', 'id')],
            'type'          => ['required', 'numeric', Rule::exists('lead_types', 'id')],
            'assigned_to'   => ['required', 'numeric', Rule::exists('users', 'id')],
            'created_by'    => ['required', 'numeric', Rule::exists('users', 'id')],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
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
            'per_page'     => ['numeric'],
            'search'       => ['nullable','string'],
            'contact'      => ['nullable', 'numeric', Rule::exists('contacts', 'id')],
            'stage'        => ['nullable', 'numeric', Rule::exists('stages', 'id')],
            'type'         => ['nullable', 'numeric', Rule::exists('lead_types', 'id')],
            'source'       => ['nullable','numeric', Rule::exists('sources', 'id')],
            'assigned_to'  => ['nullable','numeric', Rule::exists('users', 'id')],
            'created_by'   => ['nullable','numeric', Rule::exists('users', 'id')],
        ]);

        if ($validator->fails()) 
        {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }
        try
        {
            $limit = $request->input(key: 'per_page', default: 10);  
            $pageNo = $request->input(key: 'page_no', default: 1); 
            $offset = ($pageNo - 1) * $limit;

            $query = Lead::query()->with(['contact', 'stage', 'source', 'type', 'assignedTo', 'createdBy']);

            if ($request->has('search'))
            {
                $query->where(function ($query) use ($request) 
                {
                    $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhereHas('contact', function ($query) use ($request) {
                        $query->where('fname', 'like','%' . $request->search . '%')
                              ->orWhere('lname', 'like','%' . $request->search . '%')
                              ->orWhere('email', 'like','%' . $request->search . '%')
                              ->orWhere('mobile_number', 'like','%' . $request->search . '%')
                              ->orWhere('phone_number', 'like', '%' . $request->search . '%')
                              ->orWhere('company', 'like', '%' . $request->search . '%');
                    });
                });
            }

            if (isset($request->contact) && !empty($request->contact)) 
            {
                $query->where('contact', '=', $request->contact);
            }

            if (isset($request->stage) && !empty($request->stage)) 
            {
                $query->where('stage', '=', $request->stage);
            }

            if (isset($request->source) && !empty($request->source)) 
            {
                $query->where('source', '=', $request->source);
            }

            if (isset($request->type) && !empty($request->type)) 
            {
                $query->where('type', '=', $request->type);
            }

            if (isset($request->assigned_to) && !empty($request->assigned_to)) 
            {
                $query->where('assigned_to', '=', $request->assigned_to);
            }

            if (isset($request->created_by) && !empty($request->created_by)) 
            {
                $query->where('created_by', '=', $request->created_by);
            }

            if ((isset($request->from_date) && !empty($request->from_date)) && (isset($request->to_date) && !empty($request->to_date))) 
            {
                $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
            }

            $total = $query->count();
            $leads = $query->limit($limit)->offset($offset)->orderBy('created_at','DESC')->get();

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
            'lead_id' => ['required','alpha_num', Rule::exists('leads', 'id')],
        ]);

        if ($validator->fails()) 
        {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
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
            'lead_id'       => ['required','alpha_num', Rule::exists('leads', 'id')],
            'contact'       => ['nullable','numeric', Rule::exists('contacts', 'id')],
            'title'         => ['nullable','string'],
            'description'   => ['nullable','string'],
            'stage'         => ['nullable','numeric', Rule::exists('stages', 'id')],
            'source'        => ['nullable', 'numeric', Rule::exists('sources', 'id')],
            'type'          => ['nullable', 'numeric', Rule::exists('lead_types', 'id')],
            'assigned_to'   => ['nullable', 'numeric', Rule::exists('users', 'id')],
            'created_by'    => ['nullable', 'numeric', Rule::exists('users', 'id')],
        ]);

        if ($validator->fails()) 
        {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
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
            'lead_id' => ['required','alpha_num', Rule::exists('leads', 'id')],
            'stage'   => ['required', 'numeric', Rule::exists('stages', 'id')],
        ]);

        if ($validator->fails()) 
        {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
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
            'lead_id' => ['required','alpha_num', Rule::exists('leads', 'id')],
        ]);

        if ($validator->fails()) 
        {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
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
                    Activity::where('lead_id', '=', $request->lead_id)->delete();
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

    public function activitiesLeadsAccordingly(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_no' => ['required','numeric'],
            'per_page'=> ['numeric'],
            'lead_id' => ['required','alpha_num', Rule::exists('leads', 'id')],
        ]);

        if ($validator->fails()) 
        {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try
        {
            $limit = $request->input(key: 'per_page', default: 10);  
            $pageNo = $request->input(key: 'page_no', default: 1); 
            $offset = ($pageNo - 1) * $limit;

            $query = Activity::where('lead_id', '=', $request->lead_id)->with(['medium']);
            $total = $query->count();
            $leads_activities = $query->limit($limit)->offset($offset)->orderBy('id','DESC')->get();

            if (!empty($leads_activities)) 
            {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.detail.success'),
                    'total'     => $total,
                    'data'      => $leads_activities,
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
}
