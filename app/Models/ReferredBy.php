<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferredBy extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'referred_by';

    protected $fillable = [
        'referred_by',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];
}
