<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadType extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'lead_types';
    protected $fillable = [
        'type',
        'status',
    ];
}
