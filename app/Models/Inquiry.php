<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Inquiry extends Model
{
    use HasFactory;
    protected $table = 'inquiries';

    protected $fillable = [
        'name',
        'email',
        'mobile_no',
        'message',
        'inquiry_source',
        'inquiry_for',
        'no_of_resources',
        'time_period',
        'tech_stack',
        'emp_experience',
        'status',
        'add_to_lead_by',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function addedByLead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'add_to_lead_by')->withTrashed(); // Adjust the foreign key if needed
    }
}
