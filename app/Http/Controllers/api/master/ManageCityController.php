<?php

namespace App\Http\Controllers\api\master;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ManageCityController extends Controller
{
    public function list(Request $request) {
        $validator = Validator::make($request->all(), [
            'page_no'   => ['required','numeric'],
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

            $query = City::query()->with('country');

            if ($request->has('search')) {
                $query->where('city', 'like', '%' . $request->search . '%');
            }

            $total = $query->count();
            $cities = $query->limit($limit)->offset($offset)->orderBy('created_at', 'desc')->get();

            if (!empty($cities)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'total'     => $total,
                    'data'      => $cities,
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
            'country_id' => ['required','numeric', Rule::exists('countries', 'id')],
            'city'   => ['required','string','max:255', Rule::unique('cities')],
        ]);

        if ($validator->fails()) {

            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $insert = City::create([
                'country_id' => $request->country_id,
                'city' => $request->city,
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
            'city_id'   => ['required','numeric', Rule::exists('cities', 'id')],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $city = City::where('id', '=', $request->city_id)->with('country')->first();
            if (!empty($city)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.detail.success'),
                    'data'      => $city,
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
            'city_id'   => ['required','numeric', Rule::exists('cities', 'id')],
            'country_id' => ['required','numeric', Rule::exists('countries', 'id')],
            'city'      => ['required','string','max:255', Rule::unique('cities')->ignore($request->city_id)],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $city = City::where('id', '=', $request->city_id)->first();
            if (empty($city)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.update.not-found', ['entity' => 'city']),
                ], 400);
            }

            $update = City::where('id', '=', $request->city_id)->update([
                'country_id' => $request->country_id ? $request->country_id : $city->country_id,
                'city' => $request->city ? $request->city : $city->city,
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
            'city_id' => ['required','numeric', Rule::exists('cities', 'id')],
            'status'   => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $city = City::where('id', '=', $request->city_id)->first();
            if (empty($city)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.change-status.not-found', ['entity' => 'city']),
                ], 400);
            }

            $update = City::where('id', '=', $request->city_id)->update(['status' => $request->status]);

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
            'city_id' => ['required','numeric', Rule::exists('cities', 'id')],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $city = City::where('id', '=', $request->city_id)->first();
            if (empty($city)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.change-status.not-found', ['entity' => 'city']),
                ], 400);
            }

            $delete = City::where('id', '=', $request->city_id)->delete();

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
