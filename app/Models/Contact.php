<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'contacts';

    protected $fillable = [
        'source',
        'name',
        'email',
        'fname',
        'lname',
        'mobile_number',
        'phone_number',
        'designation',
        'company',
        'website',
        'linkedin',
        'country',
        'city',
        'referred_by',
        'photo',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function source() : BelongsTo
    {
        return $this->belongsTo(Source::class, 'source');
    }

    public function designation() : BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation');
    }

    public function country() : BelongsTo
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function city() : BelongsTo
    {
        return $this->belongsTo(City::class, 'city');
    }

    public function referred_by() : BelongsTo
    {
        return $this->belongsTo(ReferredBy::class, 'referred_by');
    }

    public function contactStatus() : BelongsTo
    {
        return $this->belongsTo(ContactStatus::class, 'status');
    }
}
