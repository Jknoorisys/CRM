<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class ManageContactController extends Controller
{     
    public function add(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'source'        => ['nullable','numeric'],
            'email'         => ['nullable','string','email','max:255'],
            'fname'         => ['nullable','string','max:255'],
            'lname'         => ['nullable','string','max:255'],
            'mobile_number' => ['nullable','string'],
            'phone_number'  => ['nullable','string'],
            'designation'   => ['nullable','numeric'],
            'company'       => ['nullable','string'],
            'website'       => ['nullable','string'],
            'linkedin'      => ['nullable','string'],
            'country'       => ['nullable','numeric'],
            'city'          => ['nullable','numeric'],
            'referred_by'   => ['nullable','numeric'],
            'photo'         => ['nullable','image','mimes:jpeg,png,jpg,gif,svg'],
            'status'        => ['nullable','numeric'],
        ]);

        if ($validator->fails()) 
        {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try 
        {     
            $file = $request->file('photo');
            if ($file)
            {
                $extension = $file->getClientOriginalExtension();
                $file_path = 'assets/uploads/contacts/';
                $filename = time() . '1.' . $extension;

                $upload = $file->move($file_path, $filename);
                if ($upload)
                {
                    $avatar_url = ($file_path . $filename);
                }
                else
                {
                    $avatar_url = "";
                }
            }
            
            $insert = Contact::create([
                'source'        => $request->source,
                'email'         => $request->email,
                'fname'         => $request->fname,
                'lname'         => $request->lname,
                'mobile_number' => $request->mobile_number,
                'phone_number'  => $request->phone_number,
                'designation'   => $request->designation,
                'company'       => $request->company,
                'website'       => $request->website,
                'linkedin'      => $request->linkedin,
                'country'       => $request->country,
                'city'          => $request->city,
                'referred_by'   => $request->referred_by,
                'photo'         => isset($avatar_url) ? $avatar_url : '',
                'status'        => $request->status,
                "created_at"    => date('Y-m-d H:i:s')
            ]);


            if ($insert) 
            {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.add.success'),
                ], 200);
            } 
            else 
            {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.add.failed'),
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
    
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_no'       => ['required','numeric'],
            'search'        => ['nullable','string'],
            'source'        => ['nullable', 'numeric', Rule::exists('sources', 'id')],
            'designation'   => ['nullable', 'numeric', Rule::exists('designations', 'id')],
            'country'       => ['nullable','numeric', Rule::exists('countries', 'id')],
            'city'          => ['nullable','numeric', Rule::exists('cities', 'id')],
            'referred_by'   => ['nullable','numeric', Rule::exists('referred_by', 'id')],
            'status'        => ['nullable','numeric', Rule::exists('contact_status', 'id')],
        ]);

        if ($validator->fails()) 
        {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }
        try
        {
            $limit = 10; 
            $pageNo = $request->input(key: 'page_no', default: 1); 
            $offset = ($pageNo - 1) * $limit;

            $query = Contact::query()->with(['source', 'designation', 'country', 'city', 'referred_by', 'contactStatus']);

            if ($request->has('search') && !empty($request->search))
            {
                $query->where(function ($query) use ($request) 
                {
                    $query->where('fname', 'like', '%' . $request->search . '%')
                          ->orWhere('lname', 'like', '%' . $request->search . '%');
                });
            }

            if(isset($request->source) && !empty($request->source))
            {
                $query->where('source', '=', $request->source);
            }

            if (isset($request->designation) && !empty($request->designation)) 
            {
                $query->where('designation', '=', $request->designation);
            }

            if (isset($request->country) && !empty($request->country)) 
            {
                $query->where('country', '=', $request->country);
            }

            if (isset($request->city) && !empty($request->city)) 
            {
                $query->where('city', '=', $request->city);
            }

            if (isset($request->referred_by) && !empty($request->referred_by))
            {
                $query->where('referred_by', '=', $request->referred_by);
            }

            if (isset($request->status) && !empty($request->status))
            {
                $query->where('status', '=', $request->status);
            }

            if ((isset($request->from_date) && !empty($request->from_date)) && (isset($request->to_date) && !empty($request->to_date))) 
            {
                $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
            }

            $total = $query->count();
            $contacts = $query->limit($limit)->offset($offset)->orderBy('id','DESC')->get();

            if (!empty($contacts)) 
            {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'total'     => $total,
                    'data'      => $contacts,
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

    public function view(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_id' => ['required','numeric', Rule::exists('contacts', 'id')],
        ]);

        if ($validator->fails()) 
        {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try
        {
            $contact = Contact::where('id', '=', $request->contact_id)->with(['source', 'designation', 'country', 'city', 'referred_by', 'contactStatus'])->first();
            if (!empty($contact)) 
            {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.detail.success'),
                    'data'      => $contact,
                ], 200);
            } 
            else 
            {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.detail.failed'),
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

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_id'    => ['required','numeric', Rule::exists('contacts', 'id')],
            'source'        => ['nullable','numeric', Rule::exists('sources', 'id')],
            'email'         => ['nullable','string','email','max:255',Rule::unique('contacts')->ignore($request->contact_id)],
            'fname'         => ['nullable','string','max:255'],
            'lname'         => ['nullable','string','max:255'],
            'mobile_number' => ['nullable','string'],
            'phone_number'  => ['nullable','string'],
            'designation'   => ['nullable','numeric', Rule::exists('designations', 'id')],
            'company'       => ['nullable','string'],
            'website'       => ['nullable','string'],
            'linkedin'      => ['nullable','string'],
            'country'       => ['nullable','numeric', Rule::exists('countries', 'id')],
            'city'          => ['nullable','numeric', Rule::exists('cities', 'id')],
            'referred_by'   => ['nullable','numeric', Rule::exists('referred_by', 'id')],
            'photo'         => ['nullable','image','mimes:jpeg,png,jpg,gif,svg'],
            'status'        => ['nullable','numeric', Rule::exists('contact_status', 'id')],
        ]);

        if ($validator->fails()) 
        {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }

        try
        {
            $contact = Contact::where('id', '=', $request->contact_id)->first();
            if (empty($contact)) 
            {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.update.not-found', ['entity' => 'Contact']),
                ], 400);
            }
            else
            {

                $file = $request->file('photo');
                if ($file)
                {
                    $extension = $file->getClientOriginalExtension();
                    $file_path = 'assets/uploads/contacts/';
                    $filename = time().'1.'.$extension;

                    $upload = $file->move($file_path, $filename);
                    if ($upload)
                    {
                        $avatar_url = ($file_path.$filename);
                        if (File::exists($contact->photo))
                        {
                            File::delete($contact->photo);
                        }
                    }
                    else
                    {
                        $avatar_url = $contact->photo;
                    }

                }
                else
                {
                    $avatar_url = $contact->photo;
                }

                $update = Contact::where('id', '=', $request->contact_id)->update([
                    'source'            => $request->source ? $request->source : $contact->source,
                    'email'             => $request->email ? $request->email : $contact->email,
                    'fname'             => $request->fname ? $request->fname : $contact->fname,
                    'lname'             => $request->lname ? $request->lname : $contact->lname,
                    'mobile_number'     => $request->mobile_number ? $request->mobile_number : $contact->mobile_number,
                    'phone_number'      => $request->phone_number ? $request->phone_number : $contact->phone_number,
                    'designation'       => $request->designation ? $request->designation : $contact->designation,
                    'company'           => $request->company ? $request->company : $contact->company,
                    'website'           => $request->website ? $request->website : $contact->website,
                    'linkedin'          => $request->linkedin ? $request->linkedin : $contact->linkedin,
                    'country'           => $request->country ? $request->country : $contact->country,
                    'city'              => $request->city ? $request->city : $contact->city,
                    'referred_by'       => $request->referred_by ? $request->referred_by : $contact->referred_by,
                    'photo'             => (isset($avatar_url) && !empty($avatar_url)) ? $avatar_url : $contact->photo,
                    "updated_at"        => date('Y-m-d H:i:s')
                ]);

                if ($update) 
                {
                    return response()->json([
                        'status'    => 'success',
                        'message'   => trans('msg.update.success'),
                    ], 200);
                }
                else
                {
                    return response()->json([
                        'status'    => 'failed',
                        'message'   => trans('msg.update.failed'),
                    ], 400);
                }
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

    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_id' => ['required','numeric', Rule::exists('contacts', 'id')],
            'status'     => ['required', 'numeric', Rule::exists('contact_status', 'id')],
        ]);

        if ($validator->fails()) 
        {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }
        
        try
        {
            $contact = Contact::where('id', '=', $request->contact_id)->first();
            if (empty($contact)) 
            {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.update.not-found', ['entity' => 'Contact']),
                ], 400);
            }
            else
            {
                $update = Contact::where('id', '=', $request->contact_id)->update(['status' => $request->status]);

                if ($update) 
                {
                    return response()->json([
                        'status'    => 'success',
                        'message'   => trans('msg.change-status.success'),
                    ], 200);
                } 
                else 
                {
                    return response()->json([
                        'status'    => 'failed',
                        'message'   => trans('msg.change-status.failed'),
                    ], 400);
                }
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

    public function delete(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'contact_id' => ['required','numeric', Rule::exists('contacts', 'id')],
        ]);

        if ($validator->fails()) 
        {
            $firstError = current(array_values($validator->errors()->messages()));

            return response()->json([
                'status'  => 'failed',
                'message' => $firstError[0],
            ], 400);
        }
        
        try
        {
            $contact = Contact::where('id', '=', $request->contact_id)->first();
            if (empty($contact)) 
            {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.update.not-found', ['entity' => 'Contact']),
                ], 400);
            }
            else
            {
                $delete = Contact::where('id', '=', $request->contact_id)->delete();
    
                if ($delete) 
                {
                    return response()->json([
                        'status'    => 'success',
                        'message'   => trans('msg.delete.success'),
                    ], 200);
                } 
                else 
                {
                    return response()->json([
                        'status'    => 'failed',
                        'message'   => trans('msg.delete.failed'),
                    ], 400);
                }
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
