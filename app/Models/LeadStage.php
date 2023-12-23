<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadStage extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'lead_stages';

    protected $fillable = [
        'stage',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];
}
