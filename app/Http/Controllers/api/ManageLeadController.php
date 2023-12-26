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
}
