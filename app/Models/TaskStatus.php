<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskStatus extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'task_status';

    protected $fillable = [
        'name',
        'color',
        'status',
    ];

    protected $hidden = [
        'deleted_at',
    ];
}
