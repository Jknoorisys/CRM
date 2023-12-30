<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use App\Models\Contact;
use App\Models\Activity;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

    public function scrollable_leads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_no'  => ['required','numeric'],
            'date'    => ['nullable','string', Rule::in(['today', 'yesterday', 'day_before_yesterday', 'tomorrow', 'day_after_tomorrow'])],
        ]);

        $validator->setCustomMessages([
            'date.in' => 'Please enter a valid date. Accepted values are: today, yesterday, day_before_yesterday, tomorrow, day_after_tomorrow.'
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

            if($request->date == 'today')
            {
                $date = now()->format('Y-m-d');
            }
            else if($request->date == 'yesterday')
            {
                $date = now()->subDay()->format('Y-m-d');
            }
            else if($request->date == 'day_before_yesterday')
            {
                $date = now()->subDays(2)->format('Y-m-d');
            }
            else if($request->date == 'tomorrow')
            {
                $date = now()->addDay()->format('Y-m-d');
            }
            else if($request->date == 'day_after_tomorrow')
            {
                $date = now()->addDays(2)->format('Y-m-d');
            }
            else
            {
                $date = now()->format('Y-m-d');
            }
            $query = Lead::query()->with(['contact', 'stage', 'source', 'type', 'assignedTo', 'createdBy'])
                                ->join('activities', 'leads.id', '=', 'activities.lead_id')
                                ->whereDate('activities.follow_up_date', $date);
            
            $total = $query->count();
            $leads = $query->limit($limit)->offset($offset)->orderBy('leads.id','DESC')->get();

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

    public function count(Request $request)
    {
        try
        {
            $total_contacts   = Contact::count();
            $total_users      = User::count();
            $total_leads      = Lead::count();
            $total_activities = Activity::count();
            
            return response()->json([
                'status'            => 'success',
                'total_contacts'    => $total_contacts,
                'total_users'       => $total_users,
                'total_leads'       => $total_leads,
                'total_activities'  => $total_activities,
            ], 200);
            
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
