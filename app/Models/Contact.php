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
        'email',
        'salutation',
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
        'created_by',
        'photo',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function source() : BelongsTo
    {
        return $this->belongsTo(Source::class, 'source')->withTrashed();
    }

    public function designation() : BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation')->withTrashed();
    }

    public function country() : BelongsTo
    {
        return $this->belongsTo(Country::class, 'country')->withTrashed();
    }

    public function city() : BelongsTo
    {
        return $this->belongsTo(City::class, 'city')->withTrashed();
    }

    public function referred_by() : BelongsTo
    {
        return $this->belongsTo(ReferredBy::class, 'referred_by')->withTrashed();
    }

    public function getSource() : BelongsTo
    {
        return $this->belongsTo(Source::class, 'source')->withTrashed();
    }

    public function getDesignation() : BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation')->withTrashed();
    }

    public function getCountry() : BelongsTo
    {
        return $this->belongsTo(Country::class, 'country')->withTrashed();
    }

    public function getCity() : BelongsTo
    {
        return $this->belongsTo(City::class, 'city')->withTrashed();
    }

    public function referredBy() : BelongsTo
    {
        return $this->belongsTo(ReferredBy::class, 'referred_by')->withTrashed();
    }

    public function createdBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function contactStatus() : BelongsTo
    {
        return $this->belongsTo(ContactStatus::class, 'status')->withTrashed();
    }
}
