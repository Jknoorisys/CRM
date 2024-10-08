<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stage extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'stages';
    
    protected $fillable = [
        'stage',
        'color',
        'status',
        'lead_category'
    ];

    protected $hidden = [
        'deleted_at',
    ];
}
