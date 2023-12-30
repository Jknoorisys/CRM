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
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportContact implements ToModel, WithHeadingRow
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

        return new Contact([
            'source' => $source->id,
            'designation' => $designation->id,
            'country' => $country->id,
            'city' => $city->id,
            'referred_by' => $referredBy->id,
            'email' => $row['email'],
            'fname' => $row['first_name'],
            'lname' => $row['last_name'],
            'mobile_number' => $row['mobile_number'],
            'phone_number' => $row['phone_number'],
            'company' => $row['company'],
            'website' => $row['website'],
            'linkedin' => $row['linkedin'],
            'photo' => $row['photo'] ? $row['photo'] : '',
            'status' => $status->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
