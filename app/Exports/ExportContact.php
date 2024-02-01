<?php

namespace App\Exports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportContact implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Contact::select('*')->with('getSource', 'getDesignation', 'getCountry', 'getCity', 'referredBy', 'contactStatus')->get();
    }

    public function headings(): array {
        return [
            'Source',
            'Email',
            'Salutation',
            'First Name',
            'Last Name',
            'Mobile Number',
            'Phone Number',
            'Designation',
            'Company',
            'Website',
            'LinkedIn',
            'Country',
            'City',
            'Referred By',
            'Photo',
            'Status',
        ];
    }


    public function map($contact): array {
        return [
            $contact->getSource->source ?? '',
            $contact->email ?? '',
            $contact->salutation ?? '',
            $contact->fname ?? '',
            $contact->lname ?? '',
            $contact->mobile_number ?? '',
            $contact->phone_number ?? '',
            $contact->getDesignation->designation ?? '', 
            $contact->company ?? '',
            $contact->website ?? '',
            $contact->linkedin ?? '',
            $contact->getCountry->country ?? '',
            $contact->getCity->city ?? '',
            $contact->referredBy->referred_by ?? '',
            $contact->photo ?? '',
            $contact->contactStatus->name ?? '' ,
        ];
    }

}
