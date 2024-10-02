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
        'title',
        'lead_id',
        'user_id',
        'medium',
        'stage',
        'summary',
        'attachment',
        'follow_up_date',
        'created_by',
        'action_performed_by'
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'id')->withTrashed();
    }

    public function medium(): BelongsTo
    {
        return $this->belongsTo(ActivityMedium::class, 'medium', 'id')->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class, 'stage', 'id')->withTrashed();
    }
    public function actionPerformedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_performed_by')->withTrashed();
    }
}
