<?php

namespace App\Imports;

use App\Models\City;
use App\Models\Contact;
use App\Models\ContactStatus;
use App\Models\Country;
use App\Models\Designation;
use App\Models\ReferredBy;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class ImportContact implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $source = $row['source'] ? Source::firstOrCreate(['source' => $row['source']]) : null;
        $designation = $row['designation'] ? Designation::firstOrCreate(['designation' => $row['designation']]) : null;
        $country = $row['country'] ? Country::firstOrCreate(['country' => $row['country']]) : null;
        $city = $row['city'] ? City::firstOrCreate(['country_id' => $country->id,'city' => $row['city']]) : null;
        $referredBy = $row['referred_by'] ? ReferredBy::firstOrCreate(['referred_by' => $row['referred_by']]) : null;
        $status = $row['status'] ? ContactStatus::firstOrCreate(['name' => $row['status']]) : null;

        return Contact::create([
            'source' => $source ? $source->id : null,
            'designation' => $designation ? $designation->id : null,
            'country' => $country ? $country->id : null,
            'city' => $city ? $city->id : null,
            'referred_by' => $referredBy ? $referredBy->id : null,
            'email' => $row['email'],
            'fname' => $row['first_name'],
            'lname' => $row['last_name'],
            'mobile_number' => $row['mobile_number'],
            'phone_number' => $row['phone_number'],
            'company' => $row['company'],
            'website' => $row['website'],
            'linkedin' => $row['linkedin'],
            'photo' => $row['photo'],
            'status' => $status ? $status->id : null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function rules(): array
    {
        return [
            'source' => ['nullable'],
            'designation' => ['nullable'],
            'country' => ['nullable'],
            'city' => ['nullable'],
            'referred_by' => ['nullable'],
            'email' => ['nullable'],
            'first_name' => ['nullable'],
            'last_name' => ['nullable'],
            'mobile_number' => ['nullable'],
            'phone_number' => ['nullable'],
            'company' => ['nullable'],
            'website' => 'nullable',
            'linkedin' => 'nullable',
            'photo' => 'nullable',
            'status' => 'nullable',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $errors = new MessageBag();

        foreach ($failures as $failure) {
            $errors->merge($failure->errors());
        }

        logger()->error('Validation failed during import:', ['errors' => $errors->all()]);
    }
}
