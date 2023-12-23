<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityMedium extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'activity_medium';
    protected $fillable = [
        'medium',
        'status',
    ];
}
