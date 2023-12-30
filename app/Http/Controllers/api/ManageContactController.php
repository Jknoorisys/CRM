<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ManageContactController extends Controller
{     
    public function add(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'source'        => ['required','string','max:255'],
            'email'         => ['required','string','email','max:255','unique:contacts'],
            'fname'         => ['required','string','max:255'],
            'lname'         => ['required','string','max:255'],
            'mobile_number' => ['required','string'],
            'phone_number'  => ['required','string'],
            'designation'   => ['required','string'],
            'company'       => ['required','string'],
            'website'       => ['required','string'],
            'linkedin'      => ['required','string'],
            'country'       => ['required','numeric'],
            'city'          => ['required','numeric'],
            'referred_by'   => ['required','numeric'],
            'photo'         => ['nullable','image','mimes:jpeg,png,jpg,gif,svg'],
            'status'        => ['required','numeric'],
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
            'source'        => ['nullable', 'numeric'],
            'designation'   => ['nullable', 'numeric'],
            'country'       => ['nullable','numeric'],
            'city'          => ['nullable','numeric'],
            'referred_by'   => ['nullable','numeric'],
            'status'        => ['nullable','numeric'],
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

            $query = Contact::query()->with(['source', 'designation', 'country', 'city', 'referred_by', 'contactStatus']);

            if ($request->has('search'))
            {
                $query->where(function ($query) use ($request) 
                {
                    $query->where('fname', 'like', '%' . $request->search . '%')
                          ->orWhere('lname', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->has('source')) 
            {
                $query->where('source', '=', $request->source);
            }

            if ($request->has('designation')) 
            {
                $query->where('designation', '=', $request->designation);
            }

            if ($request->has('country')) 
            {
                $query->where('country', '=', $request->country);
            }

            if ($request->has('city')) 
            {
                $query->where('city', '=', $request->city);
            }

            if ($request->has('referred_by')) 
            {
                $query->where('referred_by', '=', $request->referred_by);
            }

            if ($request->has('status')) 
            {
                $query->where('status', '=', $request->status);
            }

            if ($request->has('from_date') && $request->has('to_date')) 
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
            'contact_id' => ['required','numeric'],
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
            'contact_id'    => ['required','numeric'],
            'source'        => ['nullable','string','max:255'],
            'email'         => ['nullable','string','email','max:255','unique:contacts'],
            'fname'         => ['nullable','string','max:255'],
            'lname'         => ['nullable','string','max:255'],
            'mobile_number' => ['nullable','string'],
            'phone_number'  => ['nullable','string'],
            'designation'   => ['nullable','string'],
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
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
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
            'contact_id' => ['required','numeric'],
            'status'     => ['required', 'numeric'],
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
            'contact_id' => ['required','numeric'],
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
