<?php

namespace App\Http\Controllers\api;

use App\Exports\ExportContact;
use App\Http\Controllers\Controller;
use App\Imports\ImportContact;
use Illuminate\Support\Facades\Validator;
use App\Models\Contact;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ImportExportController extends Controller
{
    public function exportContacts() {
        try {
            $name = time();
            $file = Excel::store(new ExportContact, $name.'.xlsx', 'assets_uploads_contacts');
            if (!empty($file)) {
                $uploadUrl = 'public/assets/uploads/contacts/'.$name.'.xlsx';
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.download.success'),
                    'data'      => $uploadUrl,
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.download.failed'),
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

    public function importContacts(Request $request) {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $file = Excel::import(new ImportContact, $request->file('excel_file'));
           
            if ($file) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.import.success'),
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.import.failed'),
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
