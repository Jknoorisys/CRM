<?php

namespace App\Http\Controllers\api;

use App\Exports\ExportContact;
use App\Http\Controllers\Controller;
use App\Imports\ImportContact;
use Illuminate\Support\Facades\Validator;
use App\Models\Contact;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class ImportExportController extends Controller
{
    // public function exportContacts() {
    //     try {
    //         $name = time();
    //         $file = Excel::store(new ExportContact, $name.'.xlsx', 'assets_uploads_contacts');
    //         if (!empty($file)) {
    //             $uploadUrl = asset('assets/uploads/contacts').'/'.$name.'.xlsx';
    //             return response()->json([
    //                 'status'    => 'success',
    //                 'message'   => trans('msg.download.success'),
    //                 'data'      => $uploadUrl,
    //             ], 200);
    //         } else {
    //             return response()->json([
    //                 'status'    => 'failed',
    //                 'message'   => trans('msg.download.failed'),
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

    // New api  
    public function exportContacts()
    {
        try {
            // Fetch contacts excluding duplicates (based on email or allowing NULL)
            $exportedContacts = Contact::select('fname', 'lname', 'email', 'mobile_number')
                ->groupBy(DB::raw('COALESCE(email, "NULL")'))
                ->havingRaw('COUNT(*) = 1')
                ->get();

            // Now export the non-duplicate contacts to XLSX
            $name = time();
            $file = Excel::store(new ExportContact($exportedContacts), $name . '.xlsx', 'assets_uploads_contacts');

            if (!empty($file)) {
                $uploadUrl = asset('assets/uploads/contacts') . '/' . $name . '.xlsx';

                // Return the response with the download link
                return response()->json([
                    'status' => 'success',
                    'message' => trans('msg.download.success'),
                    'data' => [
                        'downloadUrl' => $uploadUrl,
                    ]
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => trans('msg.download.failed'),
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'failed',
                'message' => trans('msg.error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // public function importContacts(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'excel_file' => 'required|file|mimes:xlsx,xls,csv',
    //     ]);

    //     if ($validator->fails()) {
    //         $firstError = current(array_values($validator->errors()->messages()));

    //         return response()->json([
    //             'status'  => 'failed',
    //             'message' => $firstError[0],
    //         ], 400);
    //     }

    //     try {
    //         $file = Excel::import(new ImportContact, $request->file('excel_file'));

    //         if ($file) {
    //             return response()->json([
    //                 'status'    => 'success',
    //                 'message'   => trans('msg.import.success'),
    //             ], 200);
    //         } else {
    //             return response()->json([
    //                 'status'    => 'failed',
    //                 'message'   => trans('msg.import.failed'),
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

    public function importContacts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
            'user_id'   => ['nullable', 'numeric', Rule::exists('users', 'id')],
        ]);

        if ($validator->fails()) {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try {
            $file = $request->file('excel_file');
            $importedData = Excel::toArray([], $file);

            // Check if data exists
            if (empty($importedData[0])) {
                throw new \Exception('No data found in the file');
            }

            $skippedContacts = [];
            $importedContacts = 0;
            $headers = array_map('trim', $importedData[0][0]);

            // Log the headers
            Log::info('Excel Headers:', $headers);

            foreach ($importedData[0] as $index => $row) {
                if ($index === 0) {
                    continue; // Skip the header row
                }


                $emailKey = array_search('Email', $headers);
                $mobileKey = array_search('Mobile Number', $headers);

                $email = isset($row[$emailKey]) ? trim($row[$emailKey]) : null;
                $mobile = isset($row[$mobileKey]) ? trim($row[$mobileKey]) : null;


                Log::info("Email: " . $email);
                Log::info("Mobile: " . $mobile);


                $duplicate = Contact::where('email', $email)
                    ->orWhere('mobile_number', $mobile)
                    ->first();

                if ($duplicate) {
                    Log::info('Duplicate contact found', ['email' => $email, 'mobile' => $mobile]);
                    $skippedContacts[] = [
                        'name' => isset($row[array_search('First Name', $headers)]) ? $row[array_search('First Name', $headers)] . ' ' . $row[array_search('Last Name', $headers)] : '',
                        'mobile' => $mobile,
                        'email' => $email,
                        'reason' => 'Duplicate Entry',
                    ];
                    continue;
                }

                // Save the new contact
                Contact::create([
                    'source' => isset($row[array_search('Source', $headers)]) ? trim($row[array_search('Source', $headers)]) : '',
                    'email' => $email,
                    'salutation' => isset($row[array_search('Salutation', $headers)]) ? trim($row[array_search('Salutation', $headers)]) : '',
                    'fname' => isset($row[array_search('First Name', $headers)]) ? trim($row[array_search('First Name', $headers)]) : '',
                    'lname' => isset($row[array_search('Last Name', $headers)]) ? trim($row[array_search('Last Name', $headers)]) : '',
                    'mobile_number' => $mobile,
                    'phone_number' => isset($row[array_search('Phone Number', $headers)]) ? trim($row[array_search('Phone Number', $headers)]) : '',
                    'designation' => isset($row[array_search('Designation', $headers)]) ? trim($row[array_search('Designation', $headers)]) : '',
                    'company' => isset($row[array_search('Company', $headers)]) ? trim($row[array_search('Company', $headers)]) : '',
                    'website' => isset($row[array_search('Website', $headers)]) ? trim($row[array_search('Website', $headers)]) : '',
                    'linkedin' => isset($row[array_search('LinkedIn', $headers)]) ? trim($row[array_search('LinkedIn', $headers)]) : '',
                    'country' => isset($row[array_search('Country', $headers)]) ? trim($row[array_search('Country', $headers)]) : '',
                    'city' => isset($row[array_search('City', $headers)]) ? trim($row[array_search('City', $headers)]) : '',
                    'referred_by' => isset($row[array_search('Referred By', $headers)]) ? trim($row[array_search('Referred By', $headers)]) : '',
                    'created_by' => $request->user_id,
                    'status' => isset($row[array_search('Status', $headers)]) ? trim($row[array_search('Status', $headers)]) : '',
                ]);

                Log::info('New contact saved', ['email' => $email, 'mobile' => $mobile]);
                $importedContacts++;
            }

            return response()->json([
                'status' => 'success',
                'message' => "$importedContacts contacts imported successfully.",
                'skipped_contacts' => $skippedContacts,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('ImportContacts Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);

            return response()->json([
                'status' => 'failed',
                'message' => 'Something went wrong, please try again later',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
