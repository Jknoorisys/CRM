<?php

namespace App\Imports;

use App\Models\Contact;
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
        return new Contact([
            'source' => $row[''],
            'email' => $row[''],
            'fname' => $row[''],
            'lname' => $row[''],
            'mobile_number' => $row[''],
            'phone_number' => $row[''],
            'designation' => $row[''],
            'company' => $row[''],
            'website' => $row[''],
            'linkedin' => $row[''],
            'country' => $row[''],
            'city' => $row[''],
            'referred_by' => $row[''],
            'photo' => $row[''],
        ]);
    }
}
