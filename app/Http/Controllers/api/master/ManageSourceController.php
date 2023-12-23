<?php

namespace App\Http\Controllers\api\master;

use App\Http\Controllers\Controller;
use App\Models\LeadSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ManageSourceController extends Controller
{
    public function list(Request $request) {
        $validator = Validator::make($request->all(), [
            'page_no'   => ['required','numeric'],
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
            $pageNo = $request->query('page_no', 1); 
            $offset = ($pageNo - 1) * $limit;

            $query = LeadSource::query();

            if ($request->has('search')) {
                $query->where('source', 'like', '%' . $request->search . '%');
            }

            $sources = $query->limit($limit)->offset($offset)->get();
            $total = $query->count();

            if (!empty($sources)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'total'     => $total,
                    'data'      => $sources,
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
            'source'   => ['required','string','max:255', Rule::unique('lead_sources')],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try {
            $insert = LeadSource::create([
                'source' => $request->source,
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
            'source_id'   => ['required','numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try {
            $source = LeadSource::where('id', '=', $request->source_id)->first();
            if (!empty($source)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.detail.success'),
                    'data'      => $source,
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
            'source_id'   => ['required','numeric'],
            'source'      => ['required','string','max:255', Rule::unique('lead_sources')->ignore($request->source_id)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try {
            $source = LeadSource::where('id', '=', $request->source_id)->first();
            if (empty($source)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.update.not-found', ['entity' => 'lead source']),
                ], 400);
            }

            $update = LeadSource::where('id', '=', $request->source_id)->update([
                'source' => $request->source ? $request->source : $source->source,
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
            'source_id' => ['required','numeric'],
            'status'   => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try {
            $source = LeadSource::where('id', '=', $request->source_id)->first();
            if (empty($source)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.change-status.not-found', ['entity' => 'lead source']),
                ], 400);
            }

            $update = LeadSource::where('id', '=', $request->source_id)->update(['status' => $request->status]);

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
            'source_id' => ['required','numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        }

        try {
            $source = LeadSource::where('id', '=', $request->source_id)->first();
            if (empty($source)) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.change-status.not-found', ['entity' => 'source']),
                ], 400);
            }

            $delete = LeadSource::where('id', '=', $request->source_id)->delete();

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
