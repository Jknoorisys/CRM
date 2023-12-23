<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefferedBy extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'reffered_by';

    protected $fillable = [
        'reffered_by',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];
}
