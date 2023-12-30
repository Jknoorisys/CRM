<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Validator;


class DashboardController extends Controller
{
    public function active_leads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_no'      => ['required','numeric'],
            'stage'        => ['nullable','string'],
            'source'       => ['nullable', 'numeric'],
            'date'         => ['nullable', 'string'],
            'assigned_to'  => ['nullable','numeric'],
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

            $startDate = now()->subMonth();
            $endDate = now();

            $query = Lead::query()->with(['contact', 'stage', 'source', 'type', 'assignedTo', 'createdBy','latestActivity'])
                                ->whereBetween('last_contacted_date', [$startDate, $endDate]);

            if (isset($request->stage) && !empty($request->stage)) 
            {
                $query->where('stage', '=', $request->stage);
            }

            if (isset($request->soruce) && !empty($request->source)) 
            {
                $query->where('source', '=', $request->source);
            }

            if (isset($request->date) && !empty($request->date)) 
            {
                $query->whereDate('last_contacted_date', '=', $request->date);
            }

            if (isset($request->assigned_to) && !empty($request->assigned_to)) 
            {
                $query->where('assigned_to', '=', $request->assigned_to);
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
}
