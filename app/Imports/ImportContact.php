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
        $source = Source::firstOrCreate(['source' => $row['source']]);
        $designation = Designation::firstOrCreate(['designation' => $row['designation']]);
        $country = Country::firstOrCreate(['country' => $row['country']]);
        $city = City::firstOrCreate(['country_id' => $country->id,'city' => $row['city']]);
        $referredBy = ReferredBy::firstOrCreate(['referred_by' => $row['referred_by']]);
        $status = ContactStatus::firstOrCreate(['name' => $row['status']]);

        return Contact::updateOrCreate(
            ['email' => $row['email']],
            [
            'source' => $source->id ? $source->id : 1,
            'designation' => $designation->id ? $designation->id : 1,
            'country' => $country->id ? $country->id : 1,
            'city' => $city->id ? $city->id : 1,
            'referred_by' => $referredBy->id ? $referredBy->id : 1,
            'email' => $row['email'] ? $row['email'] : '',
            'fname' => $row['first_name'] ? $row['first_name'] : '',
            'lname' => $row['last_name'] ? $row['last_name'] : '',
            'mobile_number' => $row['mobile_number'] ? $row['mobile_number'] : '',
            'phone_number' => $row['phone_number'] ? $row['phone_number'] : '',
            'company' => $row['company'] ? $row['company'] : '',
            'website' => $row['website'] ? $row['website'] : '',
            'linkedin' => $row['linkedin'] ? $row['linkedin'] : '',
            'photo' => $row['photo'] ? $row['photo'] : '',
            'status' => $status->id ? $status->id : 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function rules(): array
    {
        return [
            'source' => ['required'],
            'designation' => ['required'],
            'country' => ['required'],
            'city' => ['required'],
            'referred_by' => ['required'],
            'email' => ['required'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'mobile_number' => ['required'],
            'phone_number' => ['nullable'],
            'company' => ['required'],
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
