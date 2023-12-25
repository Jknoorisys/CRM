<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'activities';
    protected $fillable = [
        'medium',
        'summary',
        'stage',
        'attachment',
        'reminder_date',
        'follow_up_date',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function medium() : BelongsTo
    {
        return $this->belongsTo(ActivityMedium::class, 'medium', 'id')->withTrashed();
    }

    public function stage() : BelongsTo
    {
        return $this->belongsTo(Stage::class, 'stage', 'id')->withTrashed();
    }
}
