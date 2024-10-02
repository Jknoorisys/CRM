<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Inquiry;
use App\Models\Lead;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class ManageInquiryController extends Controller
{
    // public function add(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name'            => ['nullable', 'string'],
    //         'email'           => ['nullable', 'string', 'email', 'max:255'],
    //         'mobile'          => ['nullable', 'numeric'],
    //         'message'         => ['nullable', 'string'],
    //         'inquiry_source'  => ['nullable', 'string', 'max:255'],
    //         'inquiry_for'     => ['nullable', 'string'],
    //         'no_of_resources' => ['nullable', 'string'],
    //         'time_period'     => ['nullable', 'string'],
    //         'tech_stack'      => ['nullable', 'string'],
    //         'emp_experience'  => ['nullable', 'string'],
    //     ]);

    //     if ($validator->fails()) {
    //         $firstError = current(array_values($validator->errors()->messages()));

    //         return response()->json([
    //             'status'  => 'failed',
    //             'message' => $firstError[0],
    //         ], 400);
    //     }

    //     try {


    //         // Correctly mapping the request fields to the database columns
    //         $insert = Inquiry::create([
    //             'name'            => $request->name,
    //             'email'           => $request->email,
    //             'mobile_no'       => $request->mobile,
    //             'message'         => $request->message,
    //             'inquiry_source'  => $request->inquiry_source,
    //             'inquiry_for'     => $request->inquiry_for,
    //             'no_of_resources' => $request->no_of_resources,
    //             'time_period'     => $request->time_period,
    //             'tech_stack'      => $request->tech_stack,
    //             'emp_experience'  => $request->emp_experience,
    //             "created_at"      => now(),
    //             "updated_at"      => now(),
    //         ]);

    //         if ($insert) {

    //             $contactExists = Contact::where('mobile_number', '=', $request->mobile || 'email', '=', $request->message)->first();
    //             if (empty($contactExists)) {
    //                 // Correctly mapping the request fields to the database columns
    //                 $insert = Contact::create([
    //                     'fname'         => $request->name,
    //                     'email'         => $request->email,
    //                     'mobile_number' => $request->mobile,
    //                     'source'         => $request->inquiry_source,
    //                     'status'        => 3,
    //                     "created_at"    => date('Y-m-d H:i:s')
    //                 ]);
    //             }

    //             return response()->json([
    //                 'status'  => 'success',
    //                 'message' => trans('msg.add.success'),
    //             ], 200);
    //         } else {
    //             return response()->json([
    //                 'status'  => 'failed',
    //                 'message' => trans('msg.add.failed'),
    //             ], 400);
    //         }
    //     } catch (\Throwable $e) {
    //         return response()->json([
    //             'status'  => 'failed',
    //             'message' => trans('msg.error'),
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function add(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name'            => ['nullable', 'string'],
            'email'           => ['nullable', 'string', 'email', 'max:255'],
            'mobile'          => ['nullable', 'numeric'],
            'message'         => ['nullable', 'string'],
            'inquiry_source'  => ['nullable', 'string', 'max:255'],
            'inquiry_for'     => ['nullable', 'string'],
            'no_of_resources' => ['nullable', 'string'],
            'time_period'     => ['nullable', 'string'],
            'tech_stack'      => ['nullable', 'string'],
            'emp_experience'  => ['nullable', 'string'],
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));
            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            // Insert the inquiry into the Inquiry table
            $insert = Inquiry::create([
                'name'            => $request->name,
                'email'           => $request->email,
                'mobile_no'       => $request->mobile,
                'message'         => $request->message,
                'inquiry_source'  => $request->inquiry_source,
                'inquiry_for'     => $request->inquiry_for,
                'no_of_resources' => $request->no_of_resources,
                'time_period'     => $request->time_period,
                'tech_stack'      => $request->tech_stack,
                'emp_experience'  => $request->emp_experience,
                "created_at"      => now(),
                "updated_at"      => now(),
            ]);

            if ($insert) {
                // Check if the email or mobile number already exists in the Contact table
                $contactExists = Contact::where('mobile_number', $request->mobile)
                    ->orWhere('email', $request->email)
                    ->first();

                // If no record exists with the same mobile number or email, insert into Contact table
                if (!$contactExists) {
                    Contact::create([
                        'fname'         => $request->name,
                        'email'         => $request->email,
                        'mobile_number' => $request->mobile,
                        'source'        => $request->inquiry_source,
                        'status'        => 3,
                        "created_at"    => now(),
                        "updated_at"    => now(),
                    ]);
                }

                return response()->json([
                    'status'  => 'success',
                    'message' => trans('msg.add.success'),
                ], 200);
            } else {
                return response()->json([
                    'status'  => 'failed',
                    'message' => trans('msg.add.failed'),
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

    public function addLead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inquiry_id'       => ['required', 'numeric'],
            'contact'       => ['required', 'numeric', Rule::exists('contacts', 'id')],
            'title'         => ['required', 'string'],
            'description'   => ['required', 'string'],
            'stage'         => ['required', 'numeric', Rule::exists('stages', 'id')],
            'source'        => ['required', 'numeric', Rule::exists('sources', 'id')],
            'type'          => ['required', 'numeric', Rule::exists('lead_types', 'id')],
            // 'assigned_to'   => ['required', 'numeric', Rule::exists('users', 'id')],
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
                // 'assigned_to'   => $request->assigned_to,
                'created_by'    => $request->created_by,
            ]);

            if ($insert) {

                $lead = Inquiry::where('id', '=', $request->inquiry_id)->first();
                if (!empty($lead)) {
                    $update = Inquiry::where('id', '=', $request->inquiry_id)->update(['status' => "add_to_lead"]);

                    if ($update) {
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
                }
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
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'page_no'      => ['required', 'numeric'],
            'per_page'     => ['nullable', 'numeric', 'min:1'],
            'search'       => ['nullable', 'string'],
            'to_date'      => ['nullable', 'date_format:Y-m-d'],
            'from_date'    => ['nullable', 'date_format:Y-m-d'],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $limit = $request->input('per_page', 10);
            $pageNo = $request->input('page_no', 1);
            $offset = ($pageNo - 1) * $limit;

            $query = Inquiry::query()
                ->with('addedByLead')  // Eager load user data
                ->where('status', '=', 'new'); 

            // Search filter
            if ($request->filled('search')) {
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('mobile_no', 'like', '%' . $request->search . '%')
                        ->orWhere('message', 'like', '%' . $request->search . '%')
                        ->orWhere('inquiry_source', 'like', '%' . $request->search . '%')
                        ->orWhere('inquiry_for', 'like', '%' . $request->search . '%');
                });
            }

            // Date filters
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereBetween('created_at', [
                    $request->from_date . ' 00:00:00',
                    $request->to_date . ' 23:59:59'
                ]);
            }

            $total = $query->count();
            $inquiries = $query->limit($limit)->offset($offset)->orderBy('created_at', 'DESC')->get();

            return response()->json([
                'status'  => 'success',
                'message' => 'Inquiries fetched successfully.',
                'total'   => $total,
                'data'    => $inquiries,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Something went wrong, please try again later.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function view(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inquiry_id' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $inquiry = Inquiry::where('id', '=', $request->inquiry_id)->with(['addedByLead'])->first();
            if (!empty($inquiry)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.detail.success'),
                    'data'      => $inquiry,
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
}
