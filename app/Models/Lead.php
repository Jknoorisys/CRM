<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'leads';
    protected $fillable = [
        'contact',
        'title',
        'description',
        'stage',
        'source',
        'type',
        'assigned_to',
        'created_by',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function contact() : BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact')->withTrashed();
    }

    public function stage() : BelongsTo
    {
        return $this->belongsTo(Stage::class, 'stage')->withTrashed();
    }

    public function source() : BelongsTo
    {
        return $this->belongsTo(Source::class, 'source')->withTrashed();
    }

    public function type() : BelongsTo
    {
        return $this->belongsTo(LeadType::class, 'type')->withTrashed();
    }

    public function assignedTo() : BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to')->withTrashed();
    }

    public function createdBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }
}
