<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use App\Models\Contact;
use App\Models\Activity;
use App\Models\Source;
use App\Models\Stage;
use App\Models\LeadType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function active_leads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_no'      => ['required', 'numeric'],
            'per_page'     => ['numeric'],
            'stage'        => ['nullable', 'numeric', Rule::exists('stages', 'id')],
            'source'       => ['nullable', 'numeric', Rule::exists('sources', 'id')],
            'to_date'      => ['nullable', 'date_format:Y-m-d'],
            'from_date'    => ['nullable', 'date_format:Y-m-d'],
            'assigned_to'  => ['nullable', 'numeric', Rule::exists('users', 'id')],
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

            $startDate = now()->subMonth();
            $endDate = now();

            $query = Lead::query()->with(['contact', 'stage', 'source', 'type', 'assignedTo', 'createdBy', 'latestActivity'])
                ->whereBetween('last_contacted_date', [$startDate, $endDate]);

            if (isset($request->stage) && !empty($request->stage)) {
                $query->where('stage', '=', $request->stage);
            }

            if (isset($request->soruce) && !empty($request->source)) {
                $query->where('source', '=', $request->source);
            }

            if ((isset($request->from_date) && !empty($request->from_date)) && (isset($request->to_date) && !empty($request->to_date))) {
                $query->whereBetween('last_contacted_date', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
            }

            if (isset($request->assigned_to) && !empty($request->assigned_to)) {
                $query->where('assigned_to', '=', $request->assigned_to);
            }

            $total = $query->count();
            $leads = $query->limit($limit)->offset($offset)->orderBy('id', 'DESC')->get();

            if (!empty($leads)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'total'     => $total,
                    'data'      => $leads,
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

    public function scrollable_leads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_no'  => ['required', 'numeric'],
            'per_page' => ['numeric'],
            'date'    => ['nullable', 'string', Rule::in(['today', 'yesterday', 'day_before_yesterday', 'tomorrow', 'day_after_tomorrow'])],
        ]);

        $validator->setCustomMessages([
            'date.in' => 'Please enter a valid date. Accepted values are: today, yesterday, day_before_yesterday, tomorrow, day_after_tomorrow.'
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

            if ($request->date == 'today') {
                $date = now()->format('Y-m-d');
            } else if ($request->date == 'yesterday') {
                $date = now()->subDay()->format('Y-m-d');
            } else if ($request->date == 'day_before_yesterday') {
                $date = now()->subDays(2)->format('Y-m-d');
            } else if ($request->date == 'tomorrow') {
                $date = now()->addDay()->format('Y-m-d');
            } else if ($request->date == 'day_after_tomorrow') {
                $date = now()->addDays(2)->format('Y-m-d');
            } else {
                $date = now()->format('Y-m-d');
            }

            $query = Lead::query()->with(['contact', 'stage', 'source', 'type', 'assignedTo', 'createdBy', 'actionPerformedBy'])
                ->join('activities', 'leads.id', '=', 'activities.lead_id')
                ->whereDate('activities.follow_up_date', $date);

            $total = $query->count();
            $leads = $query->limit($limit)->offset($offset)->orderBy('leads.id', 'DESC')->get();

            if (!empty($leads)) {
                foreach ($leads as $lead) {
                    $lead_id = $lead->lead_id;
                    $fetch_lead_records = Lead::where('id', $lead_id)->get();
                    $lead_title = $fetch_lead_records[0]->title;
                    $lead->lead_title = $lead_title;
                }
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'total'     => $total,
                    'data'      => $leads,
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

    public function count(Request $request)
    {
        try {
            $total_contacts   = Contact::count();
            $total_users      = User::where('is_admin', '!=', 'yes')->count();
            $total_leads      = Lead::count();
            $total_activities = Activity::count();

            return response()->json([
                'status'            => 'success',
                'total_contacts'    => $total_contacts,
                'total_users'       => $total_users,
                'total_leads'       => $total_leads,
                'total_activities'  => $total_activities,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function monthlyCount(Request $request)
    {
        try {
            $total_contacts   = Contact::whereMonth('created_at', Carbon::now()->month)->count();
            $total_users      = User::whereMonth('created_at', Carbon::now()->month)->where('is_admin', '!=', 'yes')->count();
            $total_leads      = Lead::whereMonth('created_at', Carbon::now()->month)->count();
            $total_activities = Activity::whereMonth('created_at', Carbon::now()->month)->count();

            return response()->json([
                'status'            => 'success',
                'total_contacts'    => $total_contacts,
                'total_users'       => $total_users,
                'total_leads'       => $total_leads,
                'total_activities'  => $total_activities,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // public function updateActivityStatus(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'activity_id' => ['required','numeric', Rule::exists('activities', 'id')],
    //     ]);

    //     if ($validator->fails()) 
    //     {
    //         $firstError = current(array_values($validator->errors()->messages()));

    //         return response()->json([
    //             'status'  => 'failed',
    //             'message' => $firstError[0],
    //         ], 400);
    //     }

    //     try
    //     {
    //         $activity = Activity::where('id', '=', $request->activity_id)->first();
    //         if (empty($activity)) 
    //         {
    //             return response()->json([
    //                 'status'    => 'failed',
    //                 'message'   => trans('msg.update.not-found', ['entity' => 'activity']),
    //             ], 400);
    //         }

    //         if($activity->is_action_performed == 'yes')
    //         {
    //             return response()->json([
    //                 'status'    => 'failed',
    //                 'message'   => trans('msg.update.activity-status-failure')
    //             ], 400);
    //         }

    //         $update = Activity::where('id', '=', $request->activity_id)->update(['is_action_performed' => 'yes']);
    //         if($update)
    //         {
    //             return response()->json([
    //                 'status'    => 'success',
    //                 'message'   => trans('msg.update.activity-status-success'),
    //             ], 200);
    //         }
    //         else
    //         {
    //             return response()->json([
    //                 'status'    => 'failed',
    //                 'message'   => trans('msg.update.failed'),
    //             ], 400);
    //         }
    //     }
    //     catch (\Throwable $e) 
    //     {
    //         return response()->json([
    //             'status'  => 'failed',
    //             'message' => trans('msg.error'),
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function updateActivityStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'activity_id' => ['required', 'numeric', Rule::exists('activities', 'id')],
            'status' => ['required', 'in:yes,no'], 
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

            // Check if the activity is already marked as "yes"
            if ($activity->is_action_performed === 'yes' && $request->status === 'yes') {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.update.activity-status-failure'),
                ], 400);
            }

            // Update the activity's status based on the request
            $update = Activity::where('id', '=', $request->activity_id)
                ->update(['is_action_performed' => $request->status]);

            if ($update) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.update.activity-status-success'),
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


    public function leadsReports(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'option'    => ['required', 'string', Rule::in(['stage', 'source', 'type'])],
            'date'      => ['required', 'string', Rule::in('this_month', 'this_year', 'overall')],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date'   => ['nullable', 'date_format:Y-m-d'],
        ]);

        $validator->setCustomMessages([
            'option.in' => 'Please enter a valid option. Accepted values are: stage, source, type.',
            'date.in'   => 'Please enter a valid date. Accepted values are: this_month, this_year, overall.',
            'start_date.date_format' => 'Please enter a valid start date format. Accepted format is: Y-m-d.',
            'end_date.date_format'   => 'Please enter a valid end date format. Accepted format is: Y-m-d.',
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            /* For lead source */

            if ($request->option == 'source' && $request->date == 'overall') {
                if ($request->has('start_date') && isset($request->start_date) && !empty($request->start_date) && $request->has('end_date') && isset($request->end_date) && !empty($request->end_date)) {
                    $startDate = $request->input('start_date');
                    $endDate = $request->input('end_date');

                    // Validate start and end dates
                    if (!$startDate || !$endDate) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => trans('msg.lead-reports.missing-dates'),
                            'report'  => [],
                        ], 400);
                    }

                    // Convert to Carbon instances to ensure proper date comparison
                    $startDate = Carbon::parse($startDate)->startOfDay();
                    $endDate = Carbon::parse($endDate)->endOfDay();

                    $get_source = Source::all();
                    $sourceCounts = [];

                    // Get total leads within the date range
                    $totalLeads = Lead::whereBetween('created_at', [$startDate, $endDate])->count();

                    if ($totalLeads > 0) {
                        if (!empty($get_source)) {
                            foreach ($get_source as $source) {
                                $source_id = $source->id;

                                // Count leads for each source within the date range
                                $count = Lead::where('source', $source_id)
                                    ->whereBetween('created_at', [$startDate, $endDate])
                                    ->count();

                                if ($count > 0) {
                                    $source_name = $source->source;
                                    $percentage = ($totalLeads > 0) ? round(($count / $totalLeads) * 100, 2) : 0;

                                    $sourceCounts[$source_name] = [
                                        'count' => $count,
                                        'percentage' => $percentage,
                                    ];
                                }
                            }

                            return response()->json([
                                'status'  => 'success',
                                'message' => trans('msg.lead-reports.success'),
                                'report'  => $sourceCounts,
                            ], 200);
                        }
                    } else {
                        return response()->json([
                            'status'  => 'error',
                            'message' => trans('msg.lead-reports.failed-overall'),
                            'report'  => [],
                        ], 400);
                    }
                } else {
                    $get_source = Source::all();
                    $sourceCounts = [];
                    $totalLeads = Lead::count();

                    if ($totalLeads > 0) {
                        if (!empty($get_source)) {
                            foreach ($get_source as $source) {
                                $source_id = $source->id;
                                $count = Lead::where('source', $source_id)->count();
                                if ($count > 0) {
                                    $source_name = $source->source;
                                    $percentage = ($totalLeads > 0) ? round(($count / $totalLeads) * 100, 2) : 0;

                                    $sourceCounts[$source_name] = [
                                        'count' => $count,
                                        'percentage' => $percentage,
                                    ];
                                }
                            }



                            return response()->json([
                                'status'  => 'success',
                                'message' => trans('msg.lead-reports.success'),
                                'report'  => $sourceCounts,
                            ], 200);
                        }
                    } else {
                        return response()->json([
                            'status'  => 'error',
                            'message' => trans('msg.lead-reports.failed-overall'),
                            'report'  => [],
                        ], 400);
                    }
                }
            }

            if ($request->option == 'source' && $request->date == 'this_month') {
                $currentDate = now();
                $currentMonth = $currentDate->format('m');

                $get_source = Source::all();
                $sourceCounts = [];
                $totalLeads = Lead::query()->whereMonth('created_at', $currentMonth)->count();

                if ($totalLeads > 0) {
                    if (!empty($get_source)) {
                        foreach ($get_source as $source) {
                            $source_id = $source->id;
                            $count = Lead::where('source', $source_id)->whereMonth('created_at', $currentMonth)->count();
                            if ($count > 0) {
                                $source_name = $source->source;
                                $percentage = ($totalLeads > 0) ? round(($count / $totalLeads) * 100, 2) : 0;

                                $sourceCounts[$source_name] = [
                                    'count' => $count,
                                    'percentage' => $percentage,
                                ];
                            }
                        }

                        return response()->json([
                            'status'  => 'success',
                            'message' => trans('msg.lead-reports.success'),
                            'report'  => $sourceCounts,
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status'  => 'error',
                        'message' => trans('msg.lead-reports.failed-monthly'),
                        'report'  => [],
                    ], 400);
                }
            }

            if ($request->option == 'source' && $request->date == 'this_year') {
                $currentDate = now();
                $currentYear = $currentDate->format('Y');

                $get_source = Source::all();
                $sourceCounts = [];
                $totalLeads = Lead::query()->whereYear('created_at', $currentYear)->count();

                if ($totalLeads > 0) {
                    if (!empty($get_source)) {
                        foreach ($get_source as $source) {
                            $source_id = $source->id;
                            $count = Lead::where('source', $source_id)->whereYear('created_at', $currentYear)->count();
                            if ($count > 0) {
                                $source_name = $source->source;
                                $percentage = ($totalLeads > 0) ? round(($count / $totalLeads) * 100, 2) : 0;

                                $sourceCounts[$source_name] = [
                                    'count'         => $count,
                                    'percentage'    => $percentage,
                                ];
                            }
                        }

                        return response()->json([
                            'status'  => 'success',
                            'message' => trans('msg.lead-reports.success'),
                            'report'  => $sourceCounts,
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status'  => 'error',
                        'message' => trans('msg.lead-reports.failed-yearly'),
                        'report'  => [],
                    ], 400);
                }
            }

            /* End of lead source */

            /* For lead stage */

            if ($request->option == 'stage' && $request->date == 'overall') {
                if ($request->has('start_date') && isset($request->start_date) && !empty($request->start_date) && $request->has('end_date') && isset($request->end_date) && !empty($request->end_date)) {
                    $startDate = $request->input('start_date');
                    $endDate = $request->input('end_date');

                    // Validate start and end dates
                    if (!$startDate || !$endDate) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => trans('msg.lead-reports.missing-dates'),
                            'report'  => [],
                        ], 400);
                    }

                    // Convert to Carbon instances to ensure proper date comparison
                    $startDate = Carbon::parse($startDate)->startOfDay();
                    $endDate = Carbon::parse($endDate)->endOfDay();

                    $get_stage = Stage::all();
                    $stageCounts = [];

                    // Get total leads within the date range
                    $totalLeads = Lead::whereBetween('created_at', [$startDate, $endDate])->count();

                    if ($totalLeads > 0) {
                        if (!empty($get_stage)) {
                            foreach ($get_stage as $stage) {
                                $stage_id = $stage->id;

                                // Count leads for each stage within the date range
                                $count = Lead::where('stage', $stage_id)
                                    ->whereBetween('created_at', [$startDate, $endDate])
                                    ->count();

                                if ($count > 0) {
                                    $stage_name = $stage->stage;
                                    $percentage = ($totalLeads > 0) ? round(($count / $totalLeads) * 100, 2) : 0;

                                    $stageCounts[$stage_name] = [
                                        'count'         => $count,
                                        'percentage'    => $percentage,
                                    ];
                                }
                            }

                            return response()->json([
                                'status'  => 'success',
                                'message' => trans('msg.lead-reports.success'),
                                'report'  => $stageCounts,
                            ], 200);
                        }
                    } else {
                        return response()->json([
                            'status'  => 'error',
                            'message' => trans('msg.lead-reports.failed-overall'),
                            'report'  => [],
                        ], 400);
                    }
                } else {
                    $get_stage = Stage::all();
                    $stageCounts = [];
                    $totalLeads = Lead::count();

                    if ($totalLeads > 0) {
                        if (!empty($get_stage)) {
                            foreach ($get_stage as $stage) {
                                $stage_id = $stage->id;
                                $count = Lead::where('stage', $stage_id)->count();
                                if ($count > 0) {
                                    $stage_name = $stage->stage;
                                    $percentage = ($totalLeads > 0) ? round(($count / $totalLeads) * 100, 2) : 0;

                                    $stageCounts[$stage_name] = [
                                        'count'         => $count,
                                        'percentage'    => $percentage,
                                    ];
                                }
                            }

                            return response()->json([
                                'status'  => 'success',
                                'message' => trans('msg.lead-reports.success'),
                                'report'  => $stageCounts,
                            ], 200);
                        }
                    } else {
                        return response()->json([
                            'status'  => 'error',
                            'message' => trans('msg.lead-reports.failed-overall'),
                            'report'  => [],
                        ], 400);
                    }
                }
            }


            if ($request->option == 'stage' && $request->date == 'this_month') {
                $currentDate = now();
                $currentMonth = $currentDate->format('m');

                $get_stage = Stage::all();
                $stageCounts = [];
                $totalLeads = Lead::query()->whereMonth('created_at', $currentMonth)->count();

                if ($totalLeads > 0) {
                    if (!empty($get_stage)) {
                        foreach ($get_stage as $stage) {
                            $stage_id = $stage->id;
                            $count = Lead::where('stage', $stage_id)->whereMonth('created_at', $currentMonth)->count();
                            if ($count > 0) {
                                $stage_name = $stage->stage;
                                $percentage = ($totalLeads > 0) ? round(($count / $totalLeads) * 100, 2) : 0;

                                $stageCounts[$stage_name] = [
                                    'count'         => $count,
                                    'percentage'    => $percentage,
                                ];
                            }
                        }

                        return response()->json([
                            'status'  => 'success',
                            'message' => trans('msg.lead-reports.success'),
                            'report'  => $stageCounts,
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status'  => 'error',
                        'message' => trans('msg.lead-reports.failed-monthly'),
                        'report'  => [],
                    ], 400);
                }
            }


            if ($request->option == 'stage' && $request->date == 'this_year') {
                $currentDate = now();
                $currentYear = $currentDate->format('Y');

                $get_stage = Stage::all();
                $stageCounts = [];
                $totalLeads = Lead::query()->whereYear('created_at', $currentYear)->count();

                if ($totalLeads > 0) {
                    if (!empty($get_stage)) {
                        foreach ($get_stage as $stage) {
                            $stage_id = $stage->id;
                            $count = Lead::where('stage', $stage_id)->whereYear('created_at', $currentYear)->count();
                            if ($count > 0) {
                                $stage_name = $stage->stage;
                                $percentage = ($totalLeads > 0) ? round(($count / $totalLeads) * 100, 2) : 0;

                                $stageCounts[$stage_name] = [
                                    'count'         => $count,
                                    'percentage'    => $percentage,
                                ];
                            }
                        }

                        return response()->json([
                            'status'  => 'success',
                            'message' => trans('msg.lead-reports.success'),
                            'report' => $stageCounts,
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status'  => 'error',
                        'message' => trans('msg.lead-reports.failed-yearly'),
                        'report'  => [],
                    ], 400);
                }
            }

            /* End of lead stage */

            /* For lead type */

            if ($request->option == 'type' && $request->date == 'overall') {
                if ($request->has('start_date') && isset($request->start_date) && !empty($request->start_date) && $request->has('end_date') && isset($request->end_date) && !empty($request->end_date)) {
                    $startDate = $request->input('start_date');
                    $endDate = $request->input('end_date');

                    // Validate start and end dates
                    if (!$startDate || !$endDate) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => trans('msg.lead-reports.missing-dates'),
                            'report'  => [],
                        ], 400);
                    }

                    // Convert to Carbon instances to ensure proper date comparison
                    $startDate = Carbon::parse($startDate)->startOfDay();
                    $endDate = Carbon::parse($endDate)->endOfDay();

                    $get_lead_types = LeadType::all();
                    $typeCounts = [];

                    // Get total leads within the date range
                    $totalLeads = Lead::whereBetween('created_at', [$startDate, $endDate])->count();

                    if ($totalLeads > 0) {
                        if (!empty($get_lead_types)) {
                            foreach ($get_lead_types as $type) {
                                $type_id = $type->id;

                                // Count leads for each type within the date range
                                $count = Lead::where('type', $type_id)
                                    ->whereBetween('created_at', [$startDate, $endDate])
                                    ->count();

                                if ($count > 0) {
                                    $type_name = $type->type;
                                    $percentage = ($totalLeads > 0) ? round(($count / $totalLeads) * 100, 2) : 0;

                                    $typeCounts[$type_name] = [
                                        'count'         => $count,
                                        'percentage'    => $percentage,
                                    ];
                                }
                            }

                            return response()->json([
                                'status'  => 'success',
                                'message' => trans('msg.lead-reports.success'),
                                'report'  => $typeCounts,
                            ], 200);
                        }
                    } else {
                        return response()->json([
                            'status'  => 'error',
                            'message' => trans('msg.lead-reports.failed-overall'),
                            'report'  => [],
                        ], 400);
                    }
                } else {
                    $get_lead_types = LeadType::all();
                    $typeCounts = [];
                    $totalLeads = Lead::count();

                    if ($totalLeads > 0) {
                        if (!empty($get_lead_types)) {
                            foreach ($get_lead_types as $type) {
                                $type_id = $type->id;
                                $count = Lead::where('type', $type_id)->count();
                                if ($count > 0) {
                                    $type_name = $type->type;
                                    $percentage = ($totalLeads > 0) ? round(($count / $totalLeads) * 100, 2) : 0;

                                    $typeCounts[$type_name] = [
                                        'count'         => $count,
                                        'percentage'    => $percentage,
                                    ];
                                }
                            }

                            return response()->json([
                                'status'  => 'success',
                                'message' => trans('msg.lead-reports.success'),
                                'report' => $typeCounts,
                            ], 200);
                        }
                    } else {
                        return response()->json([
                            'status'  => 'error',
                            'message' => trans('msg.lead-reports.failed-overall'),
                            'report'  => [],
                        ], 400);
                    }
                }
            }


            if ($request->option == 'type' && $request->date == 'this_month') {
                $currentDate = now();
                $currentMonth = $currentDate->format('m');

                $get_lead_types = LeadType::all();
                $typeCounts = [];
                $totalLeads = Lead::query()->whereMonth('created_at', $currentMonth)->count();

                if ($totalLeads > 0) {
                    if (!empty($get_lead_types)) {
                        foreach ($get_lead_types as $type) {
                            $type_id = $type->id;
                            $count = Lead::where('type', $type_id)->whereMonth('created_at', $currentMonth)->count();
                            if ($count > 0) {
                                $type_name = $type->type;
                                $percentage = ($totalLeads > 0) ? round(($count / $totalLeads) * 100, 2) : 0;

                                $typeCounts[$type_name] = [
                                    'count'         => $count,
                                    'percentage'    => $percentage,
                                ];
                            }
                        }

                        return response()->json([
                            'status'  => 'success',
                            'message' => trans('msg.lead-reports.success'),
                            'report' => $typeCounts,
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status'  => 'error',
                        'message' => trans('msg.lead-reports.failed-monthly'),
                        'report'  => [],
                    ], 400);
                }
            }


            if ($request->option == 'type' && $request->date == 'this_year') {
                $currentDate = now();
                $currentYear = $currentDate->format('Y');

                $get_lead_types = LeadType::all();
                $typeCounts = [];
                $totalLeads = Lead::query()->whereYear('created_at', $currentYear)->count();

                if ($totalLeads > 0) {
                    if (!empty($get_lead_types)) {
                        foreach ($get_lead_types as $type) {
                            $type_id = $type->id;
                            $count = Lead::where('type', $type_id)->whereYear('created_at', $currentYear)->count();
                            if ($count > 0) {
                                $type_name = $type->type;
                                $percentage = ($totalLeads > 0) ? round(($count / $totalLeads) * 100, 2) : 0;

                                $typeCounts[$type_name] = [
                                    'count'         => $count,
                                    'percentage'    => $percentage,
                                ];
                            }
                        }

                        return response()->json([
                            'status'  => 'success',
                            'message' => trans('msg.lead-reports.success'),
                            'report' => $typeCounts,
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status'  => 'error',
                        'message' => trans('msg.lead-reports.failed-yearly'),
                        'report'  => [],
                    ], 400);
                }
            }
            /* End of lead type */
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
