<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ActivityMedium;
use Illuminate\Support\Facades\Validator;
use App\Models\City;
use App\Models\ContactStatus;
use App\Models\Country;
use App\Models\Designation;
use App\Models\LeadType;
use App\Models\ReferredBy;
use App\Models\Source;
use App\Models\Stage;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\Contact;
use App\Models\UserGroups;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MastersController extends Controller
{
    public function referredBy() {
        try {
            $data = ReferredBy::where('status', 'active')->orderBy('referred_by', 'asc')->get();
            if (!empty($data)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $data,
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

    public function country() {
        try {
            $data = Country::where('status', 'active')->orderBy('country', 'asc')->get();
            if (!empty($data)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $data,
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

    public function city(Request $request) {
        $validator = Validator::make($request->all(), [
            'country_id'   => ['nullable','numeric', Rule::exists('countries', 'id')->where(function ($query) {
                $query->where('status', 'active');
            })],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {

            $country_id = $request->country_id;
            $query = City::query();

            if ($request->has('country_id')) {
                $query->where('country_id', '=' , $country_id);
            }

            $data = $query->where('status', 'active')->orderBy('city', 'asc')->get();

            if (!empty($data)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $data,
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

    public function designation() {
        try {
            $data = Designation::where('status', 'active')->orderBy('designation', 'asc')->get();
            if (!empty($data)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $data,
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

    public function contactStatus() {
        try {
            $data = ContactStatus::where('status', 'active')->orderBy('name', 'asc')->get();
            if (!empty($data)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $data,
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

    public function stage() {
        try {
            $data = Stage::where('status', 'active')->orderBy('stage', 'asc')->get();
            if (!empty($data)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $data,
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

    public function leadType() {
        try {
            $data = LeadType::where('status', 'active')->orderBy('type', 'asc')->get();
            if (!empty($data)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $data,
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

    public function source() {
        try {
            $data = Source::where('status', 'active')->orderBy('source', 'asc')->get();
            if (!empty($data)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $data,
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

    public function activityMedium() {
        try {
            $data = ActivityMedium::where('status', 'active')->orderBy('medium', 'asc')->get();
            if (!empty($data)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $data,
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

    public function taskStatus() {
        try {
            $data = TaskStatus::where('status', 'active')->orderBy('name', 'asc')->get();
            if (!empty($data)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $data,
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

    public function users() {
        try {
            $data = User::where('status', 'active')->where('is_admin', '!=', 'yes')->orderBy('name', 'asc')->get();
            if (!empty($data)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $data,
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

    public function userGroups() {
        try {
            $data = UserGroups::where('status', 'active')->orderBy('name', 'asc')->get();
            if (!empty($data)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $data,
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

    public function contacts()
    {
        try 
        {
            $data = Contact::orderBy('fname', 'asc')->get();

            if (!empty($data)) 
            {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $data,
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
